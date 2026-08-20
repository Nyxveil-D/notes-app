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
        $search = trim((string) $request->query('search', ''));

        $query = $request->user()->notes();

        if ($search !== '') {
            $query = $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $notes = $query->latest()->paginate(10)->appends(['search' => $search]);

        return view('notes.index', compact('notes', 'search'));
    }

    public function create(): View
    {
        return view('notes.create');
    }

    public function store(StoreNoteRequest $request): RedirectResponse
    {
        $request->user()->notes()->create($request->validated());

        return redirect()->route('notes.index')->with('status', 'Note created.');
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

        return redirect()->route('notes.index')->with('status', 'Note updated.');
    }

    public function destroy(Note $note): RedirectResponse
    {
        Gate::authorize('delete', $note);
        $note->delete();

        return redirect()->route('notes.index')->with('status', 'Note deleted.');
    }
}
