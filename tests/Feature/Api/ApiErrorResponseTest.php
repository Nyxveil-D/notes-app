<?php

namespace Tests\Feature\Api;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiErrorResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_api_request_returns_consistent_json(): void
    {
        $this->getJson('/api/notes')
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    public function test_invalid_bearer_token_returns_consistent_json(): void
    {
        $this->withToken('invalid-token')
            ->getJson('/api/notes')
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    public function test_unauthorized_resource_access_returns_consistent_json(): void
    {
        $user = User::factory()->create();
        $otherUsersNote = Note::factory()->create();
        $token = $user->createToken('test-client')->plainTextToken;

        $this->withToken($token)
            ->getJson("/api/notes/{$otherUsersNote->id}")
            ->assertForbidden()
            ->assertExactJson(['message' => 'This action is unauthorized.']);
    }

    public function test_missing_resource_returns_consistent_json(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-client')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/notes/999999')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Resource not found.']);
    }

    public function test_invalid_request_data_returns_consistent_json(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-client')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/notes', ['title' => '', 'content' => ''])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'The given data was invalid.')
            ->assertJsonValidationErrors(['title', 'content']);
    }

    public function test_successful_api_response_structure_remains_unchanged(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->for($user)->create();
        $token = $user->createToken('test-client')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/notes')
            ->assertOk()
            ->assertJsonPath('data.0.id', $note->id);
    }

    public function test_web_guest_redirects_to_login_instead_of_json_error(): void
    {
        $this->get('/notes')->assertRedirect(route('login'));
    }
}
