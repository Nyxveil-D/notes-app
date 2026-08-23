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
}
