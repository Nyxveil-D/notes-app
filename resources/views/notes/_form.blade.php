<label>
    <span>Title</span>
    <input name="title" value="{{ old('title', $note->title ?? '') }}" maxlength="255" required autofocus>
</label>
<label>
    <span>Content</span>
    <textarea name="content" required>{{ old('content', $note->content ?? '') }}</textarea>
</label>
@if($errors->any())
    <ul class="errors">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif
<div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem; flex-wrap: wrap;">
    <button type="submit">{{ $submitLabel }}</button>
    <a href="{{ route('notes.index') }}" class="button button-secondary">Cancel</a>
</div>
