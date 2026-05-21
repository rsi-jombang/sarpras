@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')

    {{-- Header --}}
    <div class="auth-card-header">
        <h1 class="auth-title">Welcome back</h1>
        <p class="auth-subtitle">Sign in to your Nexus Admin account</p>
    </div>

    {{-- Session Status (e.g. after logout / password reset) --}}
    @if (session('status'))
        <div class="alert d-flex align-items-center gap-2 mb-4"
            style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.25);
                    border-radius:10px;padding:12px 16px;font-size:13.5px;color:#16a34a;">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('status') }}
        </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        {{-- Email --}}
        <div class="auth-field">
            <label for="email" class="auth-label">Email Address</label>
            <div class="auth-input-wrap">
                <i class="bi bi-envelope auth-input-icon"></i>
                <input type="email" id="email" name="email" class="auth-input @error('email') is-invalid @enderror"
                    placeholder="you@company.com" value="{{ old('email') }}" required autofocus autocomplete="username" />
                @error('email')
                    <div class="auth-error">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        {{-- Password --}}
        <div class="auth-field">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label for="password" class="auth-label mb-0">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="auth-link">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="auth-input-wrap">
                <i class="bi bi-lock auth-input-icon"></i>
                <input type="password" id="password" name="password"
                    class="auth-input @error('password') is-invalid @enderror" placeholder="Enter your password" required
                    autocomplete="current-password" />
                <button type="button" class="auth-input-toggle" id="togglePassword"
                    aria-label="Toggle password visibility">
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

        {{-- Remember Me --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me" />
                <label class="form-check-label auth-check-label" for="remember_me">
                    Remember me for 30 days
                </label>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="auth-btn" id="loginBtn">
            <span class="auth-btn-text">
                <i class="bi bi-box-arrow-in-right"></i>
                Sign In
            </span>
            <span class="auth-btn-loading d-none">
                <span class="auth-spinner"></span>
                Signing in…
            </span>
        </button>

    </form>

    {{-- Divider --}}
    <div class="auth-divider">
        <span>or continue with</span>
    </div>

    {{-- Social login placeholder --}}
    <div class="d-flex gap-3">
        <button class="auth-social-btn" type="button" aria-label="Sign in with Google">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path
                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                    fill="#4285F4" />
                <path
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                    fill="#34A853" />
                <path
                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                    fill="#FBBC05" />
                <path
                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                    fill="#EA4335" />
            </svg>
            Google
        </button>
        <button class="auth-social-btn" type="button" aria-label="Sign in with GitHub">
            <i class="bi bi-github" style="font-size:17px;"></i>
            GitHub
        </button>
    </div>

    {{-- Register link --}}
    @if (Route::has('register'))
        <p class="auth-switch-text">
            Don't have an account?
            <a href="{{ route('register') }}" class="auth-link fw-semibold">
                Create account →
            </a>
        </p>
    @endif

@endsection

@push('scripts')
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword')?.addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });

        // Loading state on submit
        document.getElementById('loginForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            const text = btn.querySelector('.auth-btn-text');
            const loading = btn.querySelector('.auth-btn-loading');
            text.classList.add('d-none');
            loading.classList.remove('d-none');
            btn.disabled = true;
        });
    </script>
@endpush
