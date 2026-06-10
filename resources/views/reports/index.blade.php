@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
    <div class="container-fluid px-4 py-4">

        <div class="mb-4 animate-in">
            <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">Laporan & Export</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:12.5px">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none"
                            style="color:var(--text-muted)">Dashboard</a></li>
                    <li class="breadcrumb-item active" style="color:var(--text-secondary)">Laporan</li>
                </ol>
            </nav>
        </div>

        <div class="row g-4">
            @foreach ([
            [
                'title' => 'Laporan Permintaan Barang',
                'desc' => 'Rekapitulasi permintaan barang seluruh unit. Filter berdasarkan unit, status, prioritas, dan periode tanggal.',
                'icon' => 'bi-clipboard-list',
                'color' => 'var(--accent-primary)',
                'bg' => 'rgba(79,70,229,0.1)',
                'url' => route('reports.requests'),
                'badge' => 'PDF + Excel',
            ],
            [
                'title' => 'Laporan Kondisi Stok Barang',
                'desc' => 'Kondisi stok terkini semua barang. Filter berdasarkan tipe, kategori, dan status stok (rendah/habis).',
                'icon' => 'bi-box2',
                'color' => '#0ea5e9',
                'bg' => 'rgba(14,165,233,0.1)',
                'url' => route('reports.stocks'),
                'badge' => 'PDF + Excel',
            ],
            [
                'title' => 'Laporan Purchase Order',
                'desc' => 'Rekapitulasi pengadaan barang. Filter berdasarkan status dan periode tanggal order.',
                'icon' => 'bi-file-earmark-text',
                'color' => '#8b5cf6',
                'bg' => 'rgba(139,92,246,0.1)',
                'url' => route('reports.purchase-orders'),
                'badge' => 'PDF + Excel',
            ],
            [
                'title' => 'Laporan Stok Opname',
                'desc' => 'Hasil stok opname beserta selisih per item. Pilih opname yang ingin dicetak.',
                'icon' => 'bi-clipboard-check',
                'color' => '#22c55e',
                'bg' => 'rgba(34,197,94,0.1)',
                'url' => route('reports.opname'),
                'badge' => 'PDF + Excel',
            ],
        ] as $report)
                <div class="col-md-6">
                    <div class="card border-0 h-100 animate-in"
                        style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);border:1px solid var(--border-subtle)!important">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="flex-shrink-0 d-flex align-items-center justify-content-center"
                                    style="width:48px;height:48px;border-radius:var(--radius-md);background:{{ $report['bg'] }}">
                                    <i class="bi {{ $report['icon'] }}"
                                        style="font-size:1.3rem;color:{{ $report['color'] }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h5 class="fw-bold mb-0" style="font-size:14px;color:var(--text-primary)">
                                            {{ $report['title'] }}</h5>
                                        <span class="badge rounded-pill"
                                            style="background:{{ $report['bg'] }};color:{{ $report['color'] }};font-size:10px">
                                            {{ $report['badge'] }}
                                        </span>
                                    </div>
                                    <p class="text-muted mb-0" style="font-size:12.5px">{{ $report['desc'] }}</p>
                                </div>
                            </div>
                            <a href="{{ $report['url'] }}"
                                class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 w-100 justify-content-center"
                                style="border-radius:var(--radius-md);font-size:13px">
                                <i class="bi bi-arrow-right-circle"></i> Buka Laporan
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
@endsection
