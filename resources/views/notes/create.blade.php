@extends('layouts.app')

@section('content')
<div style="max-width: 720px; margin: 0 auto;">
    <section class="card" style="padding: 2rem;">
        <div style="margin-bottom: 1.75rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <h1 style="margin: 0 0 0.25rem 0; font-size: 1.5rem; font-weight: 700; letter-spacing: -0.02em;">New note</h1>
            <p style="margin: 0; color: var(--text-secondary); font-size: 0.9375rem;">Capture your thoughts and ideas.</p>
        </div>
        <form action="{{ route('notes.store') }}" method="POST" style="display: grid; gap: 1.25rem;">
            @csrf
            @include('notes._form', ['submitLabel' => 'Create note'])
        </form>
    </section>
</div>
@endsection
