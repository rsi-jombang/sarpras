@extends('layouts.app')

@section('title', 'Stok Opname')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">Stok Opname</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:12.5px">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none" style="color:var(--text-muted)">Dashboard</a></li>
                    <li class="breadcrumb-item active" style="color:var(--text-secondary)">Stok Opname</li>
                </ol>
            </nav>
        </div>
        @can('create', App\Models\StockOpname::class)
            <a href="{{ route('stock-opname.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
               style="border-radius:var(--radius-md);background:var(--accent-primary);border-color:var(--accent-primary)">
                <i class="bi bi-plus-lg"></i> Buat Opname Baru
            </a>
        @endcan
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4 animate-in">
        @foreach([
            ['total',       'bi-clipboard-data',  'var(--accent-primary)', 'rgba(79,70,229,0.1)',  'Total Opname'],
            ['open',        'bi-circle',          '#eab308',               'rgba(234,179,8,0.1)',  'Terbuka'],
            ['in_progress', 'bi-arrow-repeat',    '#0ea5e9',               'rgba(14,165,233,0.1)', 'Sedang Berjalan'],
            ['closed',      'bi-check-circle',    '#22c55e',               'rgba(34,197,94,0.1)',  'Selesai'],
        ] as [$key, $icon, $color, $bg, $label])
        <div class="col-6 col-md-3">
            <div class="card border-0 px-3 py-3" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);border:1px solid var(--border-subtle)!important">
                <div class="d-flex align-items-center gap-3">
                    <div class="stats-icon-wrap" style="background:{{ $bg }};color:{{ $color }}">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block" style="font-size:12.5px;font-weight:500">{{ $label }}</span>
                        <h3 class="fw-bold mb-0" id="stat-{{ $key }}" style="color:var(--text-primary)">0</h3>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Tab: Aktif & Riwayat --}}
    <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">

        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <ul class="nav nav-tabs border-0 gap-1" id="opnameTabs">
                <li class="nav-item">
                    <button class="nav-link active fw-semibold px-3 py-2" data-bs-toggle="tab" data-bs-target="#tabActive"
                            style="font-size:13px;border-radius:var(--radius-md) var(--radius-md) 0 0">
                        <i class="bi bi-arrow-repeat me-1"></i> Aktif
                        <span class="badge bg-warning-subtle text-warning ms-1" id="badgeActive" style="font-size:10px">0</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold px-3 py-2" data-bs-toggle="tab" data-bs-target="#tabHistory"
                            style="font-size:13px;border-radius:var(--radius-md) var(--radius-md) 0 0">
                        <i class="bi bi-clock-history me-1"></i> Riwayat
                        <span class="badge bg-success-subtle text-success ms-1" id="badgeHistory" style="font-size:10px">0</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body px-4 pb-4 tab-content">

            {{-- Tab Aktif --}}
            <div class="tab-pane fade show active pt-3" id="tabActive">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 w-100" id="activeTable" style="font-size:13px">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th width="170">No. Opname</th>
                                <th width="130">Tgl. Opname</th>
                                <th width="120" class="text-center">Status</th>
                                <th>Progress Hitung</th>
                                <th width="130">Dibuat Oleh</th>
                                <th width="100" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            {{-- Tab Riwayat --}}
            <div class="tab-pane fade pt-3" id="tabHistory">
                <div class="d-flex justify-content-end mb-3">
                    <div class="position-relative">
                        <i class="bi bi-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px"></i>
                        <input type="text" id="historySearch" class="form-control form-control-sm ps-5"
                               placeholder="Cari nomor opname..." style="width:220px;border-radius:50px;font-size:13px">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 w-100" id="historyTable" style="font-size:13px">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th width="160">No. Opname</th>
                                <th width="130">Tgl. Opname</th>
                                <th width="130">Tgl. Tutup</th>
                                <th>Hasil Selisih</th>
                                <th width="130">Dibuat Oleh</th>
                                <th width="130">Ditutup Oleh</th>
                                <th width="80" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
@include('stock-opname._scripts_index')
@endpush
