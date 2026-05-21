@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')

    {{-- Icon --}}
    <div class="auth-icon-wrap mb-4">
        <i class="bi bi-shield-lock-fill"></i>
    </div>

    {{-- Header --}}
    <div class="auth-card-header">
        <h1 class="auth-title">Set new password</h1>
        <p class="auth-subtitle">
            Must be at least 8 characters and include a number
            and a special character.
        </p>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('password.store') }}" id="resetForm">
        @csrf

        {{-- Token (hidden) --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}" />

        {{-- Email --}}
        <div class="auth-field">
            <label for="email" class="auth-label">Email Address</label>
            <div class="auth-input-wrap">
                <i class="bi bi-envelope auth-input-icon"></i>
                <input type="email" id="email" name="email" class="auth-input @error('email') is-invalid @enderror"
                    placeholder="you@company.com" value="{{ old('email', $request->email) }}" required autofocus
                    autocomplete="username" />
                @error('email')
                    <div class="auth-error">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        {{-- New Password --}}
        <div class="auth-field">
            <label for="password" class="auth-label">New Password</label>
            <div class="auth-input-wrap">
                <i class="bi bi-lock auth-input-icon"></i>
                <input type="password" id="password" name="password"
                    class="auth-input @error('password') is-invalid @enderror" placeholder="Min. 8 characters" required
                    autocomplete="new-password" />
                <button type="button" class="auth-input-toggle" id="togglePassword" aria-label="Toggle password">
                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                </button>
                @error('password')
                    <div class="auth-error">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        {{-- Confirm Password --}}
        <div class="auth-field">
            <label for="password_confirmation" class="auth-label">
                Confirm New Password
            </label>
            <div class="auth-input-wrap">
                <i class="bi bi-lock-fill auth-input-icon"></i>
                <input type="password" id="password_confirmation" name="password_confirmation" class="auth-input"
                    placeholder="Re-enter new password" required autocomplete="new-password" />
            </div>
        </div>

        <button type="submit" class="auth-btn" id="resetBtn">
            <span class="auth-btn-text">
                <i class="bi bi-check-circle-fill"></i>
                Reset Password
            </span>
            <span class="auth-btn-loading d-none">
                <span class="auth-spinner"></span>
                Resetting…
            </span>
        </button>

    </form>

    <p class="auth-switch-text">
        <a href="{{ route('login') }}" class="auth-link fw-semibold">
            <i class="bi bi-arrow-left"></i> Back to sign in
        </a>
    </p>

@endsection

@push('scripts')
    <script>
        document.getElementById('togglePassword')?.addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
        });

        document.getElementById('resetForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('resetBtn');
            btn.querySelector('.auth-btn-text').classList.add('d-none');
            btn.querySelector('.auth-btn-loading').classList.remove('d-none');
            btn.disabled = true;
        });
    </script>
@endpush
