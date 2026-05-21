<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Auth') — {{ config('app.name', 'Nexus Admin') }}</title>

    {{-- Vite: Bootstrap + Custom CSS --}}
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="auth-body">

    {{-- Background decorative blobs --}}
    <div class="auth-bg" aria-hidden="true">
        <div class="auth-blob auth-blob-1"></div>
        <div class="auth-blob auth-blob-2"></div>
        <div class="auth-blob auth-blob-3"></div>
    </div>

    {{-- Main wrapper: split layout --}}
    <div class="auth-wrapper">

        {{-- Left Panel (branding) — hidden on mobile --}}
        <div class="auth-left d-none d-lg-flex">

            {{-- Brand --}}
            <a href="{{ url('/') }}" class="auth-brand">
                <div class="brand-logo">
                    <i class="bi bi-hexagon-fill"></i>
                </div>
                <span class="brand-name text-white fw-bold fs-5">Nexus Admin</span>
            </a>

            {{-- Hero content --}}
            <div class="auth-hero">
                <div class="auth-hero-icon mb-4">
                    <i class="bi bi-grid-1x2-fill"></i>
                </div>
                <h2 class="auth-hero-title">
                    Powerful admin<br />dashboard for<br />modern teams.
                </h2>
                <p class="auth-hero-desc">
                    Manage users, track revenue, monitor analytics,
                    and automate workflows — all in one place.
                </p>

                {{-- Feature list --}}
                <ul class="auth-features">
                    <li>
                        <div class="auth-feature-icon">
                            <i class="bi bi-bar-chart-fill"></i>
                        </div>
                        <div>
                            <strong>Real-time Analytics</strong>
                            <span>Live data, beautiful charts</span>
                        </div>
                    </li>
                    <li>
                        <div class="auth-feature-icon">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <div>
                            <strong>Role-based Access</strong>
                            <span>Fine-grained permissions</span>
                        </div>
                    </li>
                    <li>
                        <div class="auth-feature-icon">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <div>
                            <strong>Blazing Fast</strong>
                            <span>Built on Laravel + Vite</span>
                        </div>
                    </li>
                </ul>
            </div>

            {{-- Testimonial --}}
            <div class="auth-testimonial">
                <p class="auth-testimonial-text">
                    "Nexus Admin cut our reporting time in half.
                    The interface is clean, fast, and our team loves it."
                </p>
                <div class="d-flex align-items-center gap-3 mt-3">
                    <div class="avatar" style="background:linear-gradient(135deg,#818cf8,#c084fc);">R</div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#fff;">Rizky Pratama</div>
                        <div style="font-size:12px;color:rgba(255,255,255,0.5);">CTO, Startupku.id</div>
                    </div>
                    <div class="ms-auto d-flex gap-1">
                        @for ($i = 0; $i < 5; $i++)
                            <i class="bi bi-star-fill" style="color:#fbbf24;font-size:11px;"></i>
                        @endfor
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Panel (form) --}}
        <div class="auth-right">

            {{-- Mobile brand --}}
            <a href="{{ url('/') }}" class="auth-brand-mobile d-flex d-lg-none">
                <div class="brand-logo brand-logo-sm">
                    <i class="bi bi-hexagon-fill"></i>
                </div>
                <span class="fw-bold" style="color:var(--text-primary);">Nexus Admin</span>
            </a>

            {{-- Form slot --}}
            <div class="auth-card">
                @yield('content')
            </div>

            {{-- Footer --}}
            <p class="auth-footer-text">
                © {{ date('Y') }} Nexus Admin.
                <a href="#">Privacy</a> · <a href="#">Terms</a>
            </p>

        </div>
    </div>

    @stack('scripts')
</body>

</html>
