@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')

    {{-- Header --}}
    <div class="auth-card-header">
        <h1 class="auth-title">Create account</h1>
        <p class="auth-subtitle">Join Nexus Admin — it's free to get started</p>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        {{-- Name --}}
        <div class="auth-field">
            <label for="name" class="auth-label">Full Name</label>
            <div class="auth-input-wrap">
                <i class="bi bi-person auth-input-icon"></i>
                <input type="text" id="name" name="name" class="auth-input @error('name') is-invalid @enderror"
                    placeholder="Alex Carter" value="{{ old('name') }}" required autofocus autocomplete="name" />
                @error('name')
                    <div class="auth-error">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        {{-- Email --}}
        <div class="auth-field">
            <label for="email" class="auth-label">Email Address</label>
            <div class="auth-input-wrap">
                <i class="bi bi-envelope auth-input-icon"></i>
                <input type="email" id="email" name="email" class="auth-input @error('email') is-invalid @enderror"
                    placeholder="you@company.com" value="{{ old('email') }}" required autocomplete="username" />
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
            <label for="password" class="auth-label">Password</label>
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
            <label for="password_confirmation" class="auth-label">Confirm Password</label>
            <div class="auth-input-wrap">
                <i class="bi bi-lock-fill auth-input-icon"></i>
                <input type="password" id="password_confirmation" name="password_confirmation" class="auth-input"
                    placeholder="Re-enter your password" required autocomplete="new-password" />
            </div>
        </div>

        {{-- Terms --}}
        <div class="mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="terms" name="terms" required />
                <label class="form-check-label auth-check-label" for="terms">
                    I agree to the
                    <a href="#" class="auth-link">Terms of Service</a>
                    and
                    <a href="#" class="auth-link">Privacy Policy</a>
                </label>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="auth-btn" id="registerBtn">
            <span class="auth-btn-text">
                <i class="bi bi-person-check-fill"></i>
                Create Account
            </span>
            <span class="auth-btn-loading d-none">
                <span class="auth-spinner"></span>
                Creating account…
            </span>
        </button>

    </form>

    {{-- Login link --}}
    <p class="auth-switch-text">
        Already have an account?
        <a href="{{ route('login') }}" class="auth-link fw-semibold">
            Sign in →
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

            const levels = [{
                    color: '#ef4444',
                    text: 'Weak'
                },
                {
                    color: '#f59e0b',
                    text: 'Fair'
                },
                {
                    color: '#3b82f6',
                    text: 'Good'
                },
                {
                    color: '#22c55e',
                    text: 'Strong'
                },
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
