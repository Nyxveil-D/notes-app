@extends('layouts.app')
@section('content')
<div class="actions">
    <h1>My notes</h1>
    <a class="button" href="{{ route('notes.create') }}">New note</a>
</div>

<form method="get" action="{{ route('notes.index') }}" class="search-form">
    <input type="text" name="search" placeholder="Search notes..." value="{{ $search }}">
    <button type="submit">Search</button>
</form>

@forelse($notes as $note)
    <article class="card">
        <h2><a href="{{ route('notes.show', $note) }}">{{ $note->title }}</a></h2>
        <p>{{ \Illuminate\Support\Str::limit($note->content, 160) }}</p>
        <a href="{{ route('notes.show', $note) }}">Read note</a>
    </article>
@empty
    <section class="card">
        <p>You have not created any notes yet.</p>
    </section>
@endforelse

{{ $notes->links() }}
@endsection