<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class NoteController extends Controller
{
    public function index(Request $request): View
    {
        $notes = $request->user()->notes()->latest()->paginate(10);

        return view('notes.index', compact('notes'));
    }

    public function create(): View
    {
        return view('notes.create');
    }

    public function store(StoreNoteRequest $request): RedirectResponse
    {
        $note = $request->user()->notes()->create($request->validated());

        return redirect()->route('notes.show', $note)->with('status', 'Note created.');
    }

    public function show(Note $note): View
    {
        Gate::authorize('view', $note);

        return view('notes.show', compact('note'));
    }

    public function edit(Note $note): View
    {
        Gate::authorize('update', $note);

        return view('notes.edit', compact('note'));
    }

    public function update(UpdateNoteRequest $request, Note $note): RedirectResponse
    {
        Gate::authorize('update', $note);
        $note->update($request->validated());

        return redirect()->route('notes.show', $note)->with('status', 'Note updated.');
    }

    public function destroy(Note $note): RedirectResponse
    {
        Gate::authorize('delete', $note);
        $note->delete();

        return redirect()->route('notes.index')->with('status', 'Note deleted.');
    }
}
