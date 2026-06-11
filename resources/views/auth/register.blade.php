@extends('layouts.auth')

@section('title', 'Daftar Akun')

@section('content')

    {{-- Header --}}
    <div class="auth-card-header">
        <h1 class="auth-title">Buat Akun Baru</h1>
        <p class="auth-subtitle">Daftarkan diri Anda untuk mengakses Sistem SARPRAS</p>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        {{-- Name --}}
        <div class="auth-field">
            <label for="name" class="auth-label">Nama Lengkap</label>
            <div class="auth-input-wrap">
                <i class="bi bi-person auth-input-icon"></i>
                <input type="text" id="name" name="name" class="auth-input @error('name') is-invalid @enderror"
                    placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required autofocus autocomplete="name" />
                @error('name')
                    <div class="auth-error">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        {{-- Username --}}
        <div class="auth-field">
            <label for="username" class="auth-label">Username <span class="text-danger">*</span></label>
            <div class="auth-input-wrap">
                <i class="bi bi-person auth-input-icon"></i>
                <input type="text" id="username" name="username" class="auth-input @error('username') is-invalid @enderror"
                    placeholder="Masukkan username" value="{{ old('username') }}" required autofocus autocomplete="username" />
                @error('username')
                    <div class="auth-error">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        {{-- Email --}}
        <div class="auth-field">
            <label for="email" class="auth-label">Alamat Email</label>
            <div class="auth-input-wrap">
                <i class="bi bi-envelope auth-input-icon"></i>
                <input type="email" id="email" name="email" class="auth-input @error('email') is-invalid @enderror"
                    placeholder="nama@rsi-jombang.com" value="{{ old('email') }}" autocomplete="email" />
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
            <label for="password" class="auth-label">Kata Sandi</label>
            <div class="auth-input-wrap">
                <i class="bi bi-lock auth-input-icon"></i>
                <input type="password" id="password" name="password"
                    class="auth-input @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required
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
            {{-- Password strength indicator --}}
            <div class="password-strength mt-2" id="passwordStrength" style="display:none;">
                <div class="strength-bars">
                    <div class="strength-bar" id="bar1"></div>
                    <div class="strength-bar" id="bar2"></div>
                    <div class="strength-bar" id="bar3"></div>
                    <div class="strength-bar" id="bar4"></div>
                </div>
                <span class="strength-label" id="strengthLabel"></span>
            </div>
        </div>

        {{-- Confirm Password --}}
        <div class="auth-field">
            <label for="password_confirmation" class="auth-label">Konfirmasi Kata Sandi</label>
            <div class="auth-input-wrap">
                <i class="bi bi-lock-fill auth-input-icon"></i>
                <input type="password" id="password_confirmation" name="password_confirmation" class="auth-input"
                    placeholder="Masukkan ulang kata sandi" required autocomplete="new-password" />
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="auth-btn" id="registerBtn">
            <span class="auth-btn-text">
                <i class="bi bi-person-check-fill"></i>
                Daftar Akun
            </span>
            <span class="auth-btn-loading d-none">
                <span class="auth-spinner"></span>
                Memproses…
            </span>
        </button>

    </form>

    {{-- Login link --}}
    <p class="auth-switch-text">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="auth-link fw-semibold">
            Masuk di sini →
        </a>
    </p>

@endsection

@push('scripts')
    <script>
        // Toggle password
        document.getElementById('togglePassword')?.addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
        });

        // Password strength
        document.getElementById('password')?.addEventListener('input', function() {
            const val = this.value;
            const strength = document.getElementById('passwordStrength');
            const label = document.getElementById('strengthLabel');
            const bars = ['bar1', 'bar2', 'bar3', 'bar4'].map(id => document.getElementById(id));

            if (!val) {
                strength.style.display = 'none';
                return;
            }
            strength.style.display = 'flex';

            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const levels = [
                { color: '#ef4444', text: 'Lemah' },
                { color: '#f59e0b', text: 'Cukup' },
                { color: '#3b82f6', text: 'Baik' },
                { color: '#22c55e', text: 'Kuat' },
            ];

            bars.forEach((bar, i) => {
                bar.style.background = i < score ? levels[score - 1].color : 'var(--border-color)';
            });

            label.textContent = levels[score - 1]?.text ?? '';
            label.style.color = levels[score - 1]?.color ?? '';
        });

        // Loading state
        document.getElementById('registerForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('registerBtn');
            btn.querySelector('.auth-btn-text').classList.add('d-none');
            btn.querySelector('.auth-btn-loading').classList.remove('d-none');
            btn.disabled = true;
        });
    </script>
@endpush
