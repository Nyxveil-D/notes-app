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
        $response = $this->actingAs($user)->post(route('notes.store'), ['title' => 'Project ideas', 'content' => 'Build a professional notes application.', 'user_id' => User::factory()->create()->id]);
        $note = Note::firstOrFail();
        $response->assertRedirect(route('notes.show', $note));
        $this->assertDatabaseHas('notes', ['id' => $note->id, 'user_id' => $user->id, 'title' => 'Project ideas', 'content' => 'Build a professional notes application.']);
    }

    public function test_authenticated_user_can_view_their_own_note(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->for($user)->create();
        $this->actingAs($user)->get(route('notes.show', $note))->assertOk()->assertSee($note->title);
    }

    public function test_user_cannot_view_another_users_note(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->create();
        $this->actingAs($user)->get(route('notes.show', $note))->assertForbidden();
    }

    public function test_user_cannot_update_another_users_note(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['title' => 'Private note', 'content' => 'Do not change this.']);
        $this->actingAs($user)->put(route('notes.update', $note), ['title' => 'Changed title', 'content' => 'Changed content.'])->assertForbidden();
        $this->assertDatabaseHas('notes', ['id' => $note->id, 'title' => 'Private note', 'content' => 'Do not change this.']);
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
        $this->actingAs($user)->from(route('notes.create'))->post(route('notes.store'), ['title' => '', 'content' => ''])->assertRedirect(route('notes.create'))->assertSessionHasErrors(['title', 'content']);
        $this->assertDatabaseCount('notes',0);
    }
}
