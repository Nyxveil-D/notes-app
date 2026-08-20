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
            <h1>Keep the good ideas close.</h1>
            <p>Your personal space for the thoughts, plans, and reminders that matter. Everything stays tidy when you need it.</p>
        </div>

        <div class="auth-note-stack" aria-hidden="true">
            <div class="auth-note">
                <svg class="auth-note-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Ideas worth returning to</span>
            </div>
            <div class="auth-note">
                <svg class="auth-note-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Pick up right where you left off</span>
            </div>
        </div>
    </aside>

    <section class="auth-panel" aria-labelledby="login-heading">
        <div class="auth-form-wrap">
            <p class="auth-eyebrow">Notes workspace</p>
            <h1 id="login-heading">Welcome back</h1>
            <p class="auth-form-intro">Sign in to keep your ideas organized and in reach.</p>

            <form action="{{ route('login') }}" method="POST" class="auth-form">
                @csrf
                <div class="auth-field">
                    <label for="login-email">Email address</label>
                    <input id="login-email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus @error('email') aria-invalid="true" aria-describedby="login-email-error" @enderror>
                    @error('email')
                        <p id="login-email-error" class="field-error" role="alert">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
                            </svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div class="auth-field">
                    <label for="login-password">Password</label>
                    <div class="password-field">
                        <input id="login-password" type="password" name="password" autocomplete="current-password" required @error('password') aria-invalid="true" aria-describedby="login-password-error" @enderror>
                        <button type="button" class="password-toggle" data-password-toggle="login-password" aria-label="Show password" aria-pressed="false">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 3.866-4.03 7-9 7s-9-3.134-9-7 4.03-7 9-7 9 3.134 9 7z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p id="login-password-error" class="field-error" role="alert">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
                            </svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <button type="submit" class="auth-submit">Log in</button>
            </form>

            <p class="auth-footer">New here? <a href="{{ route('register') }}">Create your space</a></p>
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
