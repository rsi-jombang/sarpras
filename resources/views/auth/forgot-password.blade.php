@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')

    {{-- Back link --}}
    <a href="{{ route('login') }}" class="auth-back-link">
        <i class="bi bi-arrow-left"></i> Back to sign in
    </a>

    {{-- Icon --}}
    <div class="auth-icon-wrap mb-4">
        <i class="bi bi-envelope-paper-fill"></i>
    </div>

    {{-- Header --}}
    <div class="auth-card-header">
        <h1 class="auth-title">Forgot your password?</h1>
        <p class="auth-subtitle">
            No worries — enter your email and we'll send you a
            reset link right away.
        </p>
    </div>

    {{-- Status --}}
    @if (session('status'))
        <div class="auth-success-msg mb-4">
            <div class="auth-success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <div style="font-weight:700;font-size:13.5px;margin-bottom:2px;">
                    Email sent!
                </div>
                <div style="font-size:13px;">{{ session('status') }}</div>
            </div>
        </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
        @csrf

        <div class="auth-field">
            <label for="email" class="auth-label">Email Address</label>
            <div class="auth-input-wrap">
                <i class="bi bi-envelope auth-input-icon"></i>
                <input type="email" id="email" name="email" class="auth-input @error('email') is-invalid @enderror"
                    placeholder="you@company.com" value="{{ old('email') }}" required autofocus />
                @error('email')
                    <div class="auth-error">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <button type="submit" class="auth-btn" id="forgotBtn">
            <span class="auth-btn-text">
                <i class="bi bi-send-fill"></i>
                Send Reset Link
            </span>
            <span class="auth-btn-loading d-none">
                <span class="auth-spinner"></span>
                Sending…
            </span>
        </button>

    </form>

    <p class="auth-switch-text">
        Remembered your password?
        <a href="{{ route('login') }}" class="auth-link fw-semibold">Sign in →</a>
    </p>

@endsection

@push('scripts')
    <script>
        document.getElementById('forgotForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('forgotBtn');
            btn.querySelector('.auth-btn-text').classList.add('d-none');
            btn.querySelector('.auth-btn-loading').classList.remove('d-none');
            btn.disabled = true;
        });
    </script>
@endpush
