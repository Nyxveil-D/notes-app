@extends('layouts.app')

@section('content')
<div style="max-width: 720px; margin: 0 auto;">
    <section class="card" style="padding: 2rem;">
        <div style="margin-bottom: 1.75rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <h1 style="margin: 0 0 0.25rem 0; font-size: 1.5rem; font-weight: 700; letter-spacing: -0.02em;">Edit note</h1>
            <p style="margin: 0; color: var(--text-secondary); font-size: 0.9375rem;">Update your note title or content.</p>
        </div>
        <form action="{{ route('notes.update', $note) }}" method="POST" style="display: grid; gap: 1.25rem;">
            @csrf
            @method('PUT')
            @include('notes._form', ['submitLabel' => 'Save changes'])
        </form>
    </section>
</div>
@endsection
