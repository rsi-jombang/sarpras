<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Auth') — Sistem SARPRAS RSI Jombang</title>

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
                    <i class="bi bi-hospital"></i>
                </div>
                <span class="brand-name text-white fw-bold fs-5">SARPRAS RSI</span>
            </a>

            {{-- Hero content --}}
            <div class="auth-hero">
                <div class="auth-hero-icon mb-4">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <h2 class="auth-hero-title">
                    Sistem Manajemen<br />Sarana & Prasarana<br />Rumah Sakit.
                </h2>
                <p class="auth-hero-desc">
                    Kelola inventaris, permintaan barang antar unit,
                    stok opname, dan purchase order — semua dalam satu platform terpadu.
                </p>

                {{-- Feature list --}}
                <ul class="auth-features">
                    <li>
                        <div class="auth-feature-icon">
                            <i class="bi bi-boxes"></i>
                        </div>
                        <div>
                            <strong>Manajemen Inventaris</strong>
                            <span>Data barang & stok terpusat</span>
                        </div>
                    </li>
                    <li>
                        <div class="auth-feature-icon">
                            <i class="bi bi-send-check-fill"></i>
                        </div>
                        <div>
                            <strong>Alur Permintaan Digital</strong>
                            <span>Request & approval real-time</span>
                        </div>
                    </li>
                    <li>
                        <div class="auth-feature-icon">
                            <i class="bi bi-clipboard2-data-fill"></i>
                        </div>
                        <div>
                            <strong>Stok Opname Terintegrasi</strong>
                            <span>Pencocokan stok fisik vs sistem</span>
                        </div>
                    </li>
                </ul>
            </div>

            {{-- Info Badge --}}
            <div class="auth-testimonial">
                <p class="auth-testimonial-text">
                    "Sistem informasi logistik untuk mendukung efisiensi
                    operasional pelayanan kesehatan di RSI Jombang."
                </p>
                <div class="d-flex align-items-center gap-3 mt-3">
                    <div class="avatar" style="background:linear-gradient(135deg,#818cf8,#c084fc);">
                        <i class="bi bi-heart-pulse-fill" style="font-size:14px;"></i>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#fff;">Rumah Sakit Islam Jombang</div>
                        <div style="font-size:12px;color:rgba(255,255,255,0.5);">Bagian Sarana & Prasarana</div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Panel (form) --}}
        <div class="auth-right">

            {{-- Mobile brand --}}
            <a href="{{ url('/') }}" class="auth-brand-mobile d-flex d-lg-none">
                <div class="brand-logo brand-logo-sm">
                    <i class="bi bi-hospital"></i>
                </div>
                <span class="fw-bold" style="color:var(--text-primary);">SARPRAS RSI</span>
            </a>

            {{-- Form slot --}}
            <div class="auth-card">
                @yield('content')
            </div>

            {{-- Footer --}}
            <p class="auth-footer-text">
                © {{ date('Y') }} Sistem SARPRAS — RSI Jombang.
            </p>

        </div>
    </div>

    @stack('scripts')
</body>

</html>
