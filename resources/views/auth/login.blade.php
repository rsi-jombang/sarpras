@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')

    {{-- Header --}}
    <div class="auth-card-header">
        <h1 class="auth-title">Selamat Datang</h1>
        <p class="auth-subtitle">Masuk ke akun Sistem SARPRAS Anda</p>
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
            <label for="email" class="auth-label">Alamat Email</label>
            <div class="auth-input-wrap">
                <i class="bi bi-envelope auth-input-icon"></i>
                <input type="email" id="email" name="email" class="auth-input @error('email') is-invalid @enderror"
                    placeholder="nama@rsi-jombang.com" value="{{ old('email') }}" required autofocus autocomplete="username" />
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
                <label for="password" class="auth-label mb-0">Kata Sandi</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="auth-link">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>
            <div class="auth-input-wrap">
                <i class="bi bi-lock auth-input-icon"></i>
                <input type="password" id="password" name="password"
                    class="auth-input @error('password') is-invalid @enderror" placeholder="Masukkan kata sandi" required
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
                    Ingat saya selama 30 hari
                </label>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="auth-btn" id="loginBtn">
            <span class="auth-btn-text">
                <i class="bi bi-box-arrow-in-right"></i>
                Masuk
            </span>
            <span class="auth-btn-loading d-none">
                <span class="auth-spinner"></span>
                Memproses…
            </span>
        </button>

    </form>

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
