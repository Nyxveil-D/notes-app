@extends('layouts.app')

@section('content')
<div class="dashboard-header">
    <div>
        <div class="dashboard-title-group">
            <h1>My notes</h1>
            <span class="count-badge">{{ $notes->total() }} {{ \Illuminate\Support\Str::plural('note', $notes->total()) }}</span>
        </div>
        <p>Organize, search, and manage your notes.</p>
    </div>
    <a class="button" href="{{ route('notes.create') }}">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        <span>New note</span>
    </a>
</div>

<section class="search-panel" aria-label="Search Notes">
    <form method="get" action="{{ route('notes.index') }}" class="search-form" role="search">
        <div class="search-input-group">
            <label for="search-notes-input">Search query</label>
            <div class="search-field-wrapper">
                <svg class="search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input id="search-notes-input" type="text" name="search" placeholder="Search by title or content..." value="{{ $search }}" autocomplete="off">
            </div>
        </div>
        <div class="search-actions">
            <button type="submit">Search</button>
            @if(trim((string) $search) !== '')
                <a href="{{ route('notes.index') }}" class="button button-secondary">Clear</a>
            @endif
        </div>
    </form>
</section>

@if($notes->count() > 0)
    <div class="notes-grid">
        @foreach($notes as $note)
            <article class="card note-card">
                <div class="note-card-header">
                    <span class="doc-icon-badge" aria-hidden="true">
                        <svg class="doc-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    <div>
                        <h2>{{ $note->title }}</h2>
                        <p class="note-excerpt">{{ \Illuminate\Support\Str::limit($note->content, 160) }}</p>
                    </div>
                </div>
                <div class="note-footer">
                    <div class="note-date-wrapper">
                        <svg class="clock-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Updated {{ $note->updated_at?->diffForHumans() ?? 'recently' }}</span>
                    </div>
                    <div class="note-card-actions">
                        <a href="{{ route('notes.edit', $note) }}" class="note-action-edit" aria-label="Edit {{ $note->title }}">Edit</a>
                        <form id="delete-note-form-{{ $note->id }}" action="{{ route('notes.destroy', $note) }}" method="POST" class="note-delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="note-action-delete" data-delete-trigger data-delete-form-id="delete-note-form-{{ $note->id }}" data-note-title="{{ $note->title }}">Delete</button>
                        </form>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
@else
    <section class="card empty-state">
        <span class="empty-icon-wrapper" aria-hidden="true">
            <svg class="empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
        </span>
        @if(trim((string) $search) !== '')
            <h2>No notes match your search</h2>
            <p>We couldn't find any notes matching "{{ $search }}". Try another keyword or clear your search.</p>
            <a href="{{ route('notes.index') }}" class="button button-secondary">Clear search</a>
        @else
            <h2>No notes created yet</h2>
            <p>Capture your thoughts, ideas, and reminders in one place.</p>
            <a class="button" href="{{ route('notes.create') }}">Create your first note</a>
        @endif
    </section>
@endif

@if($notes->hasPages())
    <nav class="pagination-container" aria-label="Notes Pagination">
        {{ $notes->links() }}
    </nav>
@endif

<div id="delete-note-modal" class="modal" hidden aria-hidden="true">
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="delete-note-modal-title" aria-describedby="delete-note-modal-description" tabindex="-1">
        <div class="modal-icon" aria-hidden="true">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>
            </svg>
        </div>
        <h2 id="delete-note-modal-title">Delete note?</h2>
        <p id="delete-note-modal-description">This permanently deletes <strong id="delete-note-name"></strong>. This action cannot be undone.</p>
        <div class="modal-actions">
            <button type="button" class="button button-secondary" data-modal-close>Cancel</button>
            <button type="button" class="button modal-delete-button" id="delete-note-confirm">Delete note</button>
        </div>
    </div>
</div>

<script>
    (() => {
        const modal = document.getElementById('delete-note-modal');
        const dialog = modal?.querySelector('.modal-dialog');
        const noteName = document.getElementById('delete-note-name');
        const confirmButton = document.getElementById('delete-note-confirm');
        let activeForm = null;
        let lastFocusedElement = null;

        if (!modal || !dialog || !noteName || !confirmButton) {
            return;
        }

        const closeModal = () => {
            modal.hidden = true;
            modal.setAttribute('aria-hidden', 'true');
            activeForm = null;
            lastFocusedElement?.focus();
        };

        const openModal = (trigger) => {
            activeForm = document.getElementById(trigger.dataset.deleteFormId);
            if (!activeForm) {
                return;
            }

            lastFocusedElement = trigger;
            noteName.textContent = trigger.dataset.noteTitle;
            modal.hidden = false;
            modal.setAttribute('aria-hidden', 'false');
            confirmButton.focus();
        };

        document.querySelectorAll('[data-delete-trigger]').forEach((trigger) => {
            trigger.addEventListener('click', () => openModal(trigger));
        });

        modal.querySelectorAll('[data-modal-close]').forEach((element) => {
            element.addEventListener('click', closeModal);
        });

        confirmButton.addEventListener('click', () => {
            if (activeForm) {
                activeForm.requestSubmit();
            }
        });

        dialog.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                event.preventDefault();
                closeModal();
                return;
            }

            if (event.key === 'Tab') {
                const focusable = [...dialog.querySelectorAll('button:not([disabled])')];
                const first = focusable[0];
                const last = focusable[focusable.length - 1];

                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first.focus();
                }
            }
        });
    })();
</script>
@endsection
