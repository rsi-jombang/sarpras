@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="mb-4 animate-in">
        <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">
            Selamat datang, {{ auth()->user()->name }} 👋
        </h1>
        <p class="text-muted mb-0" style="font-size:13px">
            <i class="bi bi-building me-1"></i>{{ auth()->user()->unit->name ?? 'Unit tidak diketahui' }}
            &nbsp;·&nbsp; {{ now()->translatedFormat('l, d F Y') }}
        </p>
    </div>

    {{-- Kartu Statistik --}}
    <div class="row g-3 mb-4 animate-in">
        @foreach([
            ['pending',   'bi-hourglass-split', '#eab308', 'rgba(234,179,8,0.1)',  'Menunggu Review',    'Permintaan belum diproses'],
            ['approved',  'bi-check-circle',    '#0ea5e9', 'rgba(14,165,233,0.1)', 'Disetujui',          'Sedang disiapkan sarpras'],
            ['ready',     'bi-box-seam',        '#8b5cf6', 'rgba(139,92,246,0.1)', 'Siap Diambil',       'Barang menunggu pengambilan'],
            ['completed', 'bi-bag-check',       '#22c55e', 'rgba(34,197,94,0.1)',  'Selesai Bulan Ini',  'Permintaan selesai bulan ini'],
        ] as [$key, $icon, $color, $bg, $label, $desc])
        <div class="col-6 col-xl-3">
            <div class="card border-0 h-100" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);border:1px solid var(--border-subtle)!important">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="stats-icon-wrap" style="background:{{ $bg }};color:{{ $color }}">
                            <i class="bi {{ $icon }}"></i>
                        </div>
                        @if($key === 'ready')
                            <span class="badge rounded-pill bg-purple-subtle text-purple" style="font-size:10px">Ambil Sekarang</span>
                        @endif
                    </div>
                    <h2 class="fw-bold mb-1" id="stat-{{ $key }}" style="color:var(--text-primary);font-size:1.8rem">—</h2>
                    <p class="fw-semibold mb-0" style="font-size:13px;color:var(--text-primary)">{{ $label }}</p>
                    <p class="text-muted mb-0 mt-1" style="font-size:11.5px">{{ $desc }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-4">

        {{-- Chart Permintaan Per Bulan --}}
        <div class="col-lg-8">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Permintaan 6 Bulan Terakhir</h5>
                        <p class="text-muted mb-0" style="font-size:12.5px">Jumlah permintaan yang diajukan per bulan.</p>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="chartMonthlyRequests" height="120"></canvas>
                </div>
            </div>
        </div>

        {{-- Chart Distribusi Status --}}
        <div class="col-lg-4">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Status Bulan Ini</h5>
                    <p class="text-muted mb-0" style="font-size:12.5px">Distribusi status permintaan.</p>
                </div>
                <div class="card-body px-4 pb-4 d-flex flex-column align-items-center">
                    <canvas id="chartStatusDist" style="max-width:220px;max-height:220px"></canvas>
                    <div id="statusLegend" class="mt-3 w-100" style="font-size:12.5px"></div>
                </div>
            </div>
        </div>

        {{-- Permintaan Terbaru --}}
        <div class="col-12">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Permintaan Terbaru</h5>
                        <p class="text-muted mb-0" style="font-size:12.5px">6 permintaan terkini dari unit Anda.</p>
                    </div>
                    <a href="{{ route('requests.index') }}" class="btn btn-sm btn-outline-primary"
                       style="border-radius:var(--radius-md);font-size:12.5px">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body px-4 pb-4">
                    <div id="recentRequestsContainer">
                        <div class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm me-2"></div> Memuat...
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Shortcut --}}
    <div class="row g-3 mt-2 animate-in">
        <div class="col-12">
            <h6 class="fw-bold mb-3" style="color:var(--text-secondary);font-size:12px;text-transform:uppercase;letter-spacing:.5px">
                Akses Cepat
            </h6>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('requests.create') }}" class="card border-0 text-decoration-none h-100"
               style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);border:1px solid var(--border-subtle)!important;transition:all .2s">
                <div class="card-body p-4 text-center">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;border-radius:var(--radius-md);background:rgba(79,70,229,0.1)">
                        <i class="bi bi-plus-lg" style="font-size:1.2rem;color:var(--accent-primary)"></i>
                    </div>
                    <p class="fw-semibold mb-0" style="font-size:13px;color:var(--text-primary)">Ajukan Permintaan</p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('requests.index') }}?status=ready" class="card border-0 text-decoration-none h-100"
               style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);border:1px solid var(--border-subtle)!important;transition:all .2s">
                <div class="card-body p-4 text-center">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;border-radius:var(--radius-md);background:rgba(139,92,246,0.1)">
                        <i class="bi bi-box-seam" style="font-size:1.2rem;color:#8b5cf6"></i>
                    </div>
                    <p class="fw-semibold mb-0" style="font-size:13px;color:var(--text-primary)">Barang Siap Diambil</p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('requests.index') }}" class="card border-0 text-decoration-none h-100"
               style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);border:1px solid var(--border-subtle)!important;transition:all .2s">
                <div class="card-body p-4 text-center">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;border-radius:var(--radius-md);background:rgba(14,165,233,0.1)">
                        <i class="bi bi-clipboard-list" style="font-size:1.2rem;color:#0ea5e9"></i>
                    </div>
                    <p class="fw-semibold mb-0" style="font-size:13px;color:var(--text-primary)">Riwayat Permintaan</p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('items.index') }}" class="card border-0 text-decoration-none h-100"
               style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);border:1px solid var(--border-subtle)!important;transition:all .2s">
                <div class="card-body p-4 text-center">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;border-radius:var(--radius-md);background:rgba(34,197,94,0.1)">
                        <i class="bi bi-box2" style="font-size:1.2rem;color:#22c55e"></i>
                    </div>
                    <p class="fw-semibold mb-0" style="font-size:13px;color:var(--text-primary)">Katalog Barang</p>
                </div>
            </a>
        </div>
    </div>

</div>
@endsection

@push('scripts')
@include('dashboard._scripts_unit')
@endpush
