@extends('layouts.app')

@section('content')
<div class="auth-layout">
    <aside class="auth-intro" aria-label="Notes App introduction">
        <div class="auth-product">
            <span class="auth-product-mark" aria-hidden="true">
                <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </span>
            <span>{{ config('app.name', 'Notes') }}</span>
        </div>

        <div class="auth-copy">
            <h1>Make room for what matters.</h1>
            <p>A calm home for quick thoughts, big plans, and every good idea in between.</p>
        </div>

        <div class="auth-note-stack" aria-hidden="true">
            <div class="auth-note">
                <svg class="auth-note-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Start with one good idea</span>
            </div>
            <div class="auth-note">
                <svg class="auth-note-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Keep every next step clear</span>
            </div>
        </div>
    </aside>

    <section class="auth-panel" aria-labelledby="register-heading">
        <div class="auth-form-wrap">
            <p class="auth-eyebrow">Notes workspace</p>
            <h1 id="register-heading">Create your space</h1>
            <p class="auth-form-intro">A private, organized home for your everyday ideas.</p>

            <form action="{{ route('register') }}" method="POST" class="auth-form">
                @csrf
                <div class="auth-field">
                    <label for="register-name">Name</label>
                    <input id="register-name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus @error('name') aria-invalid="true" aria-describedby="register-name-error" @enderror>
                    @error('name')
                        <p id="register-name-error" class="field-error" role="alert"><span>{{ $message }}</span></p>
                    @enderror
                </div>

                <div class="auth-field">
                    <label for="register-email">Email address</label>
                    <input id="register-email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required @error('email') aria-invalid="true" aria-describedby="register-email-error" @enderror>
                    @error('email')
                        <p id="register-email-error" class="field-error" role="alert"><span>{{ $message }}</span></p>
                    @enderror
                </div>

                <div class="auth-field">
                    <label for="register-password">Password</label>
                    <div class="password-field">
                        <input id="register-password" type="password" name="password" autocomplete="new-password" required @error('password') aria-invalid="true" aria-describedby="register-password-error" @enderror>
                        <button type="button" class="password-toggle" data-password-toggle="register-password" aria-label="Show password" aria-pressed="false">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 3.866-4.03 7-9 7s-9-3.134-9-7 4.03-7 9-7 9 3.134 9 7z" /></svg>
                        </button>
                    </div>
                    @error('password')
                        <p id="register-password-error" class="field-error" role="alert"><span>{{ $message }}</span></p>
                    @enderror
                </div>

                <div class="auth-field">
                    <label for="register-password-confirmation">Confirm password</label>
                    <div class="password-field">
                        <input id="register-password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
                        <button type="button" class="password-toggle" data-password-toggle="register-password-confirmation" aria-label="Show password confirmation" aria-pressed="false">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 3.866-4.03 7-9 7s-9-3.134-9-7 4.03-7 9-7 9 3.134 9 7z" /></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-submit">Create account</button>
            </form>

            <p class="auth-footer">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
        </div>
    </section>
</div>

<script>
    document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const input = document.getElementById(toggle.dataset.passwordToggle);
            const isVisible = input.type === 'text';

            input.type = isVisible ? 'password' : 'text';
            toggle.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
            toggle.setAttribute('aria-pressed', String(!isVisible));
        });
    });
</script>
@endsection

