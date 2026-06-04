@extends('layouts.app')

@section('title', 'Dashboard Sarpras')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="mb-4 animate-in">
        <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">
            Dashboard Sarpras
        </h1>
        <p class="text-muted mb-0" style="font-size:13px">
            {{ now()->translatedFormat('l, d F Y') }} &nbsp;·&nbsp;
            <span id="clockDisplay" class="fw-semibold" style="color:var(--accent-primary)"></span>
        </p>
    </div>

    {{-- Kartu Statistik Utama --}}
    <div class="row g-3 mb-4 animate-in">
        @foreach([
            ['pending_requests',  'bi-hourglass-split',      '#eab308', 'rgba(234,179,8,0.1)',  'Permintaan Pending',  route('requests.index').'?status=pending'],
            ['ready_requests',    'bi-box-seam',             '#8b5cf6', 'rgba(139,92,246,0.1)', 'Siap Diambil',        route('requests.index').'?status=ready'],
            ['low_stock_items',   'bi-exclamation-triangle', '#f97316', 'rgba(249,115,22,0.1)', 'Stok Rendah',         route('items.index').'?stock_status=low'],
            ['empty_stock_items', 'bi-x-circle',             '#ef4444', 'rgba(239,68,68,0.1)',  'Stok Habis',          route('items.index').'?stock_status=empty'],
            ['draft_pos',         'bi-file-earmark-text',   '#0ea5e9', 'rgba(14,165,233,0.1)', 'PO Draft',            route('purchase-orders.index').'?status=draft'],
            ['active_pos',        'bi-truck',                '#22c55e', 'rgba(34,197,94,0.1)',  'PO Aktif',            route('purchase-orders.index').'?status=ordered'],
            ['open_opname',       'bi-clipboard-check',     '#6366f1', 'rgba(99,102,241,0.1)', 'Opname Berjalan',     route('stock-opname.index')],
            ['total_items',       'bi-box2',                 '#64748b', 'rgba(100,116,139,0.1)','Total Barang',        route('items.index')],
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

        {{-- Chart Permintaan Per Bulan --}}
        <div class="col-lg-8">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Tren Permintaan (6 Bulan)</h5>
                    <p class="text-muted mb-0" style="font-size:12.5px">Total, selesai, dan ditolak per bulan.</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="chartMonthlyRequests" height="130"></canvas>
                </div>
            </div>
        </div>

        {{-- Chart Distribusi Stok --}}
        <div class="col-lg-4">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Kondisi Stok</h5>
                    <p class="text-muted mb-0" style="font-size:12.5px">Distribusi status stok seluruh barang.</p>
                </div>
                <div class="card-body px-4 pb-4 d-flex flex-column align-items-center">
                    <canvas id="chartStockStatus" style="max-width:200px;max-height:200px"></canvas>
                    <div id="stockLegend" class="mt-3 w-100" style="font-size:12.5px"></div>
                </div>
            </div>
        </div>

        {{-- Chart Pergerakan Stok 7 Hari --}}
        <div class="col-lg-6">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Pergerakan Stok (7 Hari)</h5>
                    <p class="text-muted mb-0" style="font-size:12.5px">Barang masuk & keluar per hari.</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="chartStockMovements" height="160"></canvas>
                </div>
            </div>
        </div>

        {{-- Chart Top 5 Barang --}}
        <div class="col-lg-6">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Top 5 Barang Paling Diminta</h5>
                    <p class="text-muted mb-0" style="font-size:12.5px">Berdasarkan jumlah bulan ini.</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="chartTopItems" height="160"></canvas>
                </div>
            </div>
        </div>

        {{-- Permintaan Pending --}}
        <div class="col-lg-6">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Permintaan Perlu Review</h5>
                        <p class="text-muted mb-0" style="font-size:12.5px">Permintaan pending menunggu keputusan.</p>
                    </div>
                    <a href="{{ route('requests.index') }}?status=pending" class="btn btn-sm btn-outline-primary"
                       style="border-radius:var(--radius-md);font-size:12.5px">Lihat Semua</a>
                </div>
                <div class="card-body px-4 pb-4">
                    <div id="pendingRequestsList">
                        <div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div> Memuat...</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stok Rendah --}}
        <div class="col-lg-6">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Barang Stok Rendah</h5>
                        <p class="text-muted mb-0" style="font-size:12.5px">Barang yang perlu segera diadakan.</p>
                    </div>
                    <a href="{{ route('items.index') }}?stock_status=low" class="btn btn-sm btn-outline-warning"
                       style="border-radius:var(--radius-md);font-size:12.5px">Lihat Semua</a>
                </div>
                <div class="card-body px-4 pb-4">
                    <div id="lowStockList">
                        <div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div> Memuat...</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pergerakan Stok Terbaru --}}
        <div class="col-12">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Pergerakan Stok Terbaru</h5>
                    <p class="text-muted mb-0" style="font-size:12.5px">Aktivitas masuk & keluar barang terkini.</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <div id="recentMovementsList">
                        <div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div> Memuat...</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
@include('dashboard._scripts_sarpras')
@endpush
