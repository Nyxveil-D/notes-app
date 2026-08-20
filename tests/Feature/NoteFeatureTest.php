<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_notes(): void
    {
        $this->get(route('notes.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_note(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('notes.store'), [
            'title' => 'Project ideas',
            'content' => 'Build a professional notes application.',
            'user_id' => User::factory()->create()->id,
        ]);

        $response->assertRedirect(route('notes.index'));
        $response->assertSessionHas('status', 'Note created.');
        $this->assertDatabaseHas('notes', [
            'user_id' => $user->id,
            'title' => 'Project ideas',
            'content' => 'Build a professional notes application.',
        ]);
    }

    public function test_note_show_endpoint_is_not_available(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->for($user)->create();

        $this->actingAs($user)->get("/notes/{$note->id}")->assertStatus(405);
    }

    public function test_authenticated_user_can_edit_their_own_note(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->for($user)->create(['title' => 'Project ideas']);

        $this->actingAs($user)
            ->get(route('notes.edit', $note))
            ->assertOk()
            ->assertSee('Edit note')
            ->assertSee('Project ideas');
    }

    public function test_authenticated_user_can_update_their_own_note(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->for($user)->create(['title' => 'Draft', 'content' => 'Original content']);

        $response = $this->actingAs($user)->put(route('notes.update', $note), [
            'title' => 'Final draft',
            'content' => 'Updated content',
        ]);

        $response->assertRedirect(route('notes.index'));
        $response->assertSessionHas('status', 'Note updated.');
        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => 'Final draft',
            'content' => 'Updated content',
        ]);
    }

    public function test_authenticated_user_can_delete_their_own_note(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('notes.destroy', $note));

        $response->assertRedirect(route('notes.index'));
        $response->assertSessionHas('status', 'Note deleted.');
        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function test_user_cannot_update_another_users_note(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['title' => 'Private note', 'content' => 'Do not change this.']);

        $this->actingAs($user)
            ->put(route('notes.update', $note), ['title' => 'Changed title', 'content' => 'Changed content.'])
            ->assertForbidden();

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => 'Private note',
            'content' => 'Do not change this.',
        ]);
    }

    public function test_user_cannot_delete_another_users_note(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->create();

        $this->actingAs($user)->delete(route('notes.destroy', $note))->assertForbidden();

        $this->assertDatabaseHas('notes', ['id' => $note->id]);
    }

    public function test_note_validation_rejects_invalid_input(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('notes.create'))
            ->post(route('notes.store'), ['title' => '', 'content' => ''])
            ->assertRedirect(route('notes.create'))
            ->assertSessionHasErrors(['title', 'content']);

        $this->assertDatabaseCount('notes', 0);
    }

    public function test_user_can_search_their_notes_by_title(): void
    {
        $user = User::factory()->create();
        $matchingNote = Note::factory()->for($user)->create(['title' => 'Laravel project ideas', 'content' => 'Some content']);
        Note::factory()->for($user)->create(['title' => 'Python notes', 'content' => 'Other content']);

        $response = $this->actingAs($user)->get(route('notes.index', ['search' => 'Laravel']));

        $response->assertOk();
        $response->assertSee($matchingNote->title);
        $this->assertCount(1, $response->viewData('notes'));
    }

    public function test_user_can_search_their_notes_by_content(): void
    {
        $user = User::factory()->create();
        $matchingNote = Note::factory()->for($user)->create(['title' => 'Work notes', 'content' => 'Laravel framework documentation']);
        Note::factory()->for($user)->create(['title' => 'Personal', 'content' => 'Other content']);

        $response = $this->actingAs($user)->get(route('notes.index', ['search' => 'Laravel']));

        $response->assertOk();
        $response->assertSee($matchingNote->title);
        $this->assertCount(1, $response->viewData('notes'));
    }

    public function test_another_users_matching_note_is_not_returned_in_search(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Note::factory()->for($user1)->create(['title' => 'User 1 Laravel note', 'content' => 'Content']);
        Note::factory()->for($user2)->create(['title' => 'User 2 Laravel note', 'content' => 'Content']);

        $response = $this->actingAs($user1)->get(route('notes.index', ['search' => 'Laravel']));

        $response->assertOk();
        $this->assertCount(1, $response->viewData('notes'));
        $this->assertSame($user1->id, $response->viewData('notes')[0]->user_id);
    }

    public function test_empty_search_returns_all_paginated_notes(): void
    {
        $user = User::factory()->create();
        Note::factory()->for($user)->create(['title' => 'Note 1']);
        Note::factory()->for($user)->create(['title' => 'Note 2']);

        $response = $this->actingAs($user)->get(route('notes.index', ['search' => '']));

        $response->assertOk();
        $this->assertCount(2, $response->viewData('notes'));
    }

    public function test_search_query_preserved_in_pagination(): void
    {
        $user = User::factory()->create();

        for ($index = 0; $index < 15; $index++) {
            Note::factory()->for($user)->create(['title' => "Laravel note {$index}", 'content' => 'Content']);
        }

        $response = $this->actingAs($user)->get(route('notes.index', ['search' => 'Laravel']));

        $response->assertOk();
        $paginationLinks = $response->viewData('notes')->links()->render();
        $this->assertStringContainsString('search=Laravel', $paginationLinks);
    }

    public function test_authenticated_notes_index_has_no_cache_headers(): void
    {
        $user = User::factory()->create();
        Note::factory()->for($user)->create(['title' => 'Test note', 'content' => 'Content']);

        $response = $this->actingAs($user)->get(route('notes.index'));

        $response->assertOk();
        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
        $this->assertStringContainsString('max-age=0', $cacheControl);
    }

    public function test_guest_login_page_does_not_have_no_store_cache_directive(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertStringNotContainsString('no-store', $cacheControl ?? '');
    }
}
