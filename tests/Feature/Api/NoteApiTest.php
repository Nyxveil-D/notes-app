<?php

namespace Tests\Feature\Api;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_notes_api(): void
    {
        $this->getJson('/api/notes')->assertUnauthorized()->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_authenticated_user_can_list_only_their_own_notes(): void
    {
        $user = User::factory()->create();
        $ownNote = Note::factory()->for($user)->create();
        $otherNote = Note::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/notes')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'title', 'content', 'created_at', 'updated_at']]])
            ->assertJsonPath('data.0.id', $ownNote->id)
            ->assertJsonMissing(['id' => $otherNote->id]);
    }

    public function test_authenticated_user_cannot_view_another_users_note(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->create();

        $this->actingAs($user)->getJson("/api/notes/{$note->id}")->assertForbidden();
    }

    public function test_authenticated_user_can_create_a_note_for_themselves(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/notes', ['title' => 'API note', 'content' => 'Created through the API.'])
            ->assertCreated()
            ->assertJsonPath('message', 'Note created successfully')
            ->assertJsonPath('data.title', 'API note')
            ->assertJsonPath('data.content', 'Created through the API.');

        $this->assertDatabaseHas('notes', ['user_id' => $user->id, 'title' => 'API note']);
    }

    public function test_authenticated_user_cannot_create_a_note_for_another_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/notes', ['title' => 'Owned note', 'content' => 'Still mine.', 'user_id' => $otherUser->id])
            ->assertCreated();

        $this->assertDatabaseHas('notes', ['user_id' => $user->id, 'title' => 'Owned note']);
        $this->assertDatabaseMissing('notes', ['user_id' => $otherUser->id, 'title' => 'Owned note']);
    }

    public function test_authenticated_user_can_view_their_own_note(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->for($user)->create();

        $this->actingAs($user)
            ->getJson("/api/notes/{$note->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $note->id);
    }

    public function test_authenticated_user_can_update_their_own_note(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->for($user)->create();

        $this->actingAs($user)
            ->patchJson("/api/notes/{$note->id}", ['title' => 'Updated API note', 'content' => 'Updated content.'])
            ->assertOk()
            ->assertJsonPath('message', 'Note updated successfully')
            ->assertJsonPath('data.title', 'Updated API note');

        $this->assertDatabaseHas('notes', ['id' => $note->id, 'title' => 'Updated API note']);
    }

    public function test_authenticated_user_cannot_update_another_users_note(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['title' => 'Private note', 'content' => 'Private content.']);

        $this->actingAs($user)
            ->patchJson("/api/notes/{$note->id}", ['title' => 'Changed', 'content' => 'Changed content.'])
            ->assertForbidden();

        $this->assertDatabaseHas('notes', ['id' => $note->id, 'title' => 'Private note']);
    }

    public function test_authenticated_user_can_delete_their_own_note(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->for($user)->create();

        $this->actingAs($user)
            ->deleteJson("/api/notes/{$note->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Note deleted successfully');

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function test_authenticated_user_cannot_delete_another_users_note(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->create();

        $this->actingAs($user)->deleteJson("/api/notes/{$note->id}")->assertForbidden();

        $this->assertDatabaseHas('notes', ['id' => $note->id]);
    }

    public function test_validation_rejects_invalid_data_as_json(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/notes', ['title' => '', 'content' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'content']);
    }

    public function test_search_returns_only_matching_own_notes(): void
    {
        $user = User::factory()->create();
        $matchingNote = Note::factory()->for($user)->create(['title' => 'Laravel API', 'content' => 'Content']);
        Note::factory()->for($user)->create(['title' => 'PHP', 'content' => 'Other content']);
        Note::factory()->create(['title' => 'Laravel private', 'content' => 'Other user']);

        $this->actingAs($user)
            ->getJson('/api/notes?search=Laravel')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchingNote->id);
    }

    public function test_pagination_preserves_the_search_query(): void
    {
        $user = User::factory()->create();
        Note::factory()->for($user)->count(11)->create(['title' => 'Laravel note', 'content' => 'Content']);

        $this->actingAs($user)
            ->getJson('/api/notes?search=Laravel&page=2')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('current_page', 2)
            ->assertJsonPath('path', url('/api/notes'))
            ->assertJsonPath('total', 11)
            ->assertJsonFragment(['url' => url('/api/notes?search=Laravel&page=1')]);
    }

    public function test_missing_note_returns_not_found_json(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->getJson('/api/notes/999999')->assertNotFound();
    }
}
