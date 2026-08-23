<?php

namespace Tests\Feature\Api;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SanctumAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_returns_a_bearer_token(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Postman',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Token created successfully')
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonStructure(['token']);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_api_login_rejects_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'incorrect-password',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_bearer_token_authenticates_notes_requests(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->for($user)->create();
        $token = $user->createToken('Postman')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/notes')
            ->assertOk()
            ->assertJsonPath('data.0.id', $note->id);
    }

    public function test_authenticated_token_can_log_out(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('Postman')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJson(['message' => 'Logged out successfully.']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_revokes_only_the_current_token(): void
    {
        $user = User::factory()->create();
        $currentToken = $user->createToken('Current device')->plainTextToken;
        $otherToken = $user->createToken('Other device')->plainTextToken;

        $this->withToken($currentToken)->postJson('/api/logout')->assertOk();
        $this->app['auth']->forgetGuards();

        $this->withToken($currentToken)->getJson('/api/notes')->assertUnauthorized();
        $this->app['auth']->forgetGuards();
        $this->withToken($currentToken)->postJson('/api/logout')->assertUnauthorized();
        $this->app['auth']->forgetGuards();
        $this->withToken($otherToken)->getJson('/api/notes')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_logout_without_a_current_access_token_does_not_fail(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJson(['message' => 'Logged out successfully.']);
    }

    public function test_unauthenticated_client_cannot_log_out(): void
    {
        $this->postJson('/api/logout')
            ->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_invalid_bearer_token_cannot_log_out(): void
    {
        $this->withToken('invalid-token')
            ->postJson('/api/logout')
            ->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_notes_api_rejects_requests_without_a_token_or_session(): void
    {
        $this->getJson('/api/notes')->assertUnauthorized();
    }

    public function test_bearer_token_cannot_bypass_note_ownership_policy(): void
    {
        $user = User::factory()->create();
        $otherUsersNote = Note::factory()->create();
        $token = $user->createToken('Postman')->plainTextToken;

        $this->withToken($token)
            ->getJson("/api/notes/{$otherUsersNote->id}")
            ->assertForbidden();
    }

    public function test_web_session_authentication_still_protects_web_notes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/notes')->assertOk();
    }

    public function test_authenticated_api_requests_are_rate_limited_with_json_errors(): void
    {
        $user = User::factory()->create();

        for ($attempt = 0; $attempt < 60; $attempt++) {
            $this->actingAs($user)->getJson('/api/notes')->assertOk();
        }

        $this->actingAs($user)
            ->getJson('/api/notes')
            ->assertStatus(429)
            ->assertHeader('Retry-After')
            ->assertJson(['message' => 'Too Many Requests.']);
    }

    public function test_different_authenticated_users_have_separate_api_limits(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $this->actingAs($firstUser)->getJson('/api/notes')->assertOk();
        $this->actingAs($secondUser)->getJson('/api/notes')->assertOk();
    }

    public function test_api_login_has_a_stricter_email_and_ip_rate_limit(): void
    {
        $email = 'limited@example.com';
        User::factory()->create(['email' => $email]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/api/login', [
                'email' => $email,
                'password' => 'incorrect-password',
            ])->assertUnprocessable();
        }

        $this->postJson('/api/login', [
            'email' => $email,
            'password' => 'incorrect-password',
        ])->assertStatus(429)
            ->assertHeader('Retry-After')
            ->assertJson(['message' => 'Too Many Requests.']);
    }

    public function test_api_login_limit_is_separate_for_a_different_email_at_the_same_ip(): void
    {
        $firstEmail = 'first@example.com';
        $secondEmail = 'second@example.com';
        User::factory()->create(['email' => $firstEmail]);
        User::factory()->create(['email' => $secondEmail]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/api/login', [
                'email' => $firstEmail,
                'password' => 'incorrect-password',
            ])->assertUnprocessable();
        }

        $this->postJson('/api/login', [
            'email' => $secondEmail,
            'password' => 'incorrect-password',
        ])->assertUnprocessable();
    }

    public function test_web_routes_are_not_throttled_by_the_api_limiter(): void
    {
        $user = User::factory()->create();

        for ($attempt = 0; $attempt < 61; $attempt++) {
            $this->actingAs($user)->getJson('/api/notes');
        }

        $this->actingAs($user)->get('/notes')->assertOk();
    }
}
