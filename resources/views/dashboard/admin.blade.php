@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="mb-4 animate-in">
        <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">Dashboard Admin</h1>
        <p class="text-muted mb-0" style="font-size:13px">
            {{ now()->translatedFormat('l, d F Y') }} &nbsp;·&nbsp;
            Ringkasan operasional sistem manajemen sarpras.
        </p>
    </div>

    {{-- Filter Periode --}}
    <div class="card border-0 mb-4 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);border:1px solid var(--border-subtle)">
        <div class="card-body px-4 py-3">
            <div class="row g-3 align-items-end">
                <div class="col-auto">
                    <label class="form-label fw-semibold mb-1" style="font-size:12.5px;color:var(--text-secondary)">Bulan</label>
                    <select id="filterMonth" class="form-select form-select-sm" style="width:auto;min-width:150px;font-size:13px">
                        <option value="1" {{ now()->month == 1 ? 'selected' : '' }}>Januari</option>
                        <option value="2" {{ now()->month == 2 ? 'selected' : '' }}>Februari</option>
                        <option value="3" {{ now()->month == 3 ? 'selected' : '' }}>Maret</option>
                        <option value="4" {{ now()->month == 4 ? 'selected' : '' }}>April</option>
                        <option value="5" {{ now()->month == 5 ? 'selected' : '' }}>Mei</option>
                        <option value="6" {{ now()->month == 6 ? 'selected' : '' }}>Juni</option>
                        <option value="7" {{ now()->month == 7 ? 'selected' : '' }}>Juli</option>
                        <option value="8" {{ now()->month == 8 ? 'selected' : '' }}>Agustus</option>
                        <option value="9" {{ now()->month == 9 ? 'selected' : '' }}>September</option>
                        <option value="10" {{ now()->month == 10 ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ now()->month == 11 ? 'selected' : '' }}>November</option>
                        <option value="12" {{ now()->month == 12 ? 'selected' : '' }}>Desember</option>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label fw-semibold mb-1" style="font-size:12.5px;color:var(--text-secondary)">Tahun</label>
                    <select id="filterYear" class="form-select form-select-sm" style="width:auto;min-width:120px;font-size:13px">
                        @for($y = now()->year; $y >= now()->year - 2; $y--)
                            <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <button type="button" id="btnApplyFilter" class="btn btn-primary d-flex align-items-center gap-1"
                            style="border-radius:var(--radius-md);font-size:13px;height:38px">
                        <i class="bi bi-funnel"></i> Terapkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Utama --}}
    <div class="row g-3 mb-4 animate-in">
        @foreach([
            ['total_requests',     'bi-clipboard',    'var(--accent-primary)', 'rgba(79,70,229,0.1)',  'Total Permintaan',   route('requests.index')],
            ['pending_requests',   'bi-hourglass-split',   '#eab308',               'rgba(234,179,8,0.1)',  'Pending Review',     route('requests.index').'?status=pending'],
            ['completed_requests', 'bi-bag-check',         '#22c55e',               'rgba(34,197,94,0.1)',  'Selesai Bulan Ini',  route('requests.index').'?status=completed'],
            ['total_items',        'bi-box2',              '#0ea5e9',               'rgba(14,165,233,0.1)', 'Total Barang',       route('items.index')],
            ['low_stock_items',    'bi-exclamation-triangle','#f97316',             'rgba(249,115,22,0.1)', 'Stok Rendah',        route('items.index').'?stock_status=low'],
            ['total_pos',          'bi-file-earmark-text', '#8b5cf6',               'rgba(139,92,246,0.1)', 'Total PO',           route('purchase-orders.index')],
            ['total_users',        'bi-people',            '#64748b',               'rgba(100,116,139,0.1)','Total User',        ''],
            ['total_units',        'bi-building',          '#6366f1',               'rgba(99,102,241,0.1)', 'Total Unit',         route('units.index')],
        ] as [$key, $icon, $color, $bg, $label, $url])
        <div class="col-6 col-md-4 col-xl-3">
            <a href="{{ $url }}" class="card border-0 text-decoration-none h-100"
               style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);border:1px solid var(--border-subtle)!important;transition:transform .15s,box-shadow .15s"
               onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='var(--shadow-md)'"
               onmouseout="this.style.transform='';this.style.boxShadow='var(--shadow-sm)'">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon-wrap flex-shrink-0" style="background:{{ $bg }};color:{{ $color }}">
                            <i class="bi {{ $icon }}"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0" id="stat-{{ str_replace('_','-',$key) }}" style="color:var(--text-primary)">—</h3>
                            <p class="text-muted mb-0" style="font-size:12px">{{ $label }}</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <div class="row g-4">

        {{-- Chart Permintaan 12 Bulan --}}
        <div class="col-12">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Tren Permintaan (12 Bulan)</h5>
                        <p class="text-muted mb-0" style="font-size:12.5px">Perbandingan total permintaan dan yang diselesaikan per bulan.</p>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="chartMonthlyRequests" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- Chart PO Per Bulan --}}
        <div class="col-lg-7">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Purchase Order (6 Bulan)</h5>
                    <p class="text-muted mb-0" style="font-size:12.5px">Jumlah PO dan total nilai pengadaan per bulan.</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="chartMonthlyPOs" height="160"></canvas>
                </div>
            </div>
        </div>

        {{-- Chart Distribusi Status + Top Unit --}}
        <div class="col-lg-5">
            <div class="card border-0 mb-4 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Status Permintaan Bulan Ini</h5>
                </div>
                <div class="card-body px-4 pb-4 d-flex flex-column align-items-center">
                    <canvas id="chartStatusDist" style="max-width:200px;max-height:200px"></canvas>
                    <div id="statusLegend" class="mt-3 w-100" style="font-size:12px"></div>
                </div>
            </div>

            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Top 5 Unit Aktif</h5>
                    <p class="text-muted mb-0" style="font-size:12.5px">Unit terbanyak mengajukan bulan ini.</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="chartTopUnits" height="160"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
@include('dashboard._scripts_admin')
@endpush
