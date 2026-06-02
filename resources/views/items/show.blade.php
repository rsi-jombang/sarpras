@extends('layouts.app')

@section('title', 'Detail Barang - ' . $item->name)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">{{ $item->name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:12.5px">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none" style="color:var(--text-muted)">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}" class="text-decoration-none" style="color:var(--text-muted)">Barang</a></li>
                    <li class="breadcrumb-item active" style="color:var(--text-secondary)">{{ $item->name }}</li>
                </ol>
            </nav>
        </div>
        @can('update', $item)
            <button type="button" id="btnEditItem" class="btn btn-outline-warning d-flex align-items-center gap-1"
                    style="border-radius:var(--radius-md);font-size:13px">
                <i class="bi bi-pencil"></i> Edit Barang
            </button>
        @endcan
    </div>

    <div class="row g-4">

        {{-- Kolom Kiri: Info Barang --}}
        <div class="col-lg-4">

            {{-- Gambar & Info Dasar --}}
            <div class="card border-0 mb-4 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-body p-4 text-center">
                    @if($item->image_path)
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}"
                             class="mb-3" style="width:120px;height:120px;object-fit:cover;border-radius:var(--radius-lg);border:2px solid var(--border-subtle)">
                    @else
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                             style="width:120px;height:120px;border-radius:var(--radius-lg);background:var(--bg-muted);border:2px solid var(--border-subtle)">
                            <i class="bi bi-box-seam" style="font-size:2.5rem;color:var(--text-muted)"></i>
                        </div>
                    @endif
                    <h5 class="fw-bold mb-1" style="color:var(--text-primary)">{{ $item->name }}</h5>
                    <p class="text-muted mb-2" style="font-size:12.5px">{{ $item->code }}</p>
                    <div class="d-flex justify-content-center gap-2">
                        @if($item->item_type->value === 'consumable')
                            <span class="badge rounded-pill bg-info-subtle text-info px-3 py-1">Habis Pakai</span>
                        @else
                            <span class="badge rounded-pill bg-purple-subtle text-purple px-3 py-1">Inventaris</span>
                        @endif
                        @if($item->is_active)
                            <span class="badge rounded-pill bg-success-subtle text-success px-3 py-1">Aktif</span>
                        @else
                            <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-1">Nonaktif</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Info Detail --}}
            <div class="card border-0 mb-4 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3" style="color:var(--text-secondary);font-size:12px;text-transform:uppercase;letter-spacing:.5px">Informasi Barang</h6>
                    @foreach([
                        ['bi-tag',       'Kategori',   $item->category->name],
                        ['bi-rulers',    'Satuan',     $item->unit_of_measure],
                        ['bi-calendar3', 'Ditambahkan',$item->created_at->format('d M Y')],
                    ] as [$icon, $label, $value])
                        <div class="d-flex gap-2 mb-2 align-items-start" style="font-size:13px">
                            <i class="bi {{ $icon }} text-muted mt-1" style="width:16px;flex-shrink:0"></i>
                            <span class="text-muted" style="min-width:100px;flex-shrink:0">{{ $label }}</span>
                            <span class="fw-medium" style="color:var(--text-primary)">{{ $value }}</span>
                        </div>
                    @endforeach
                    @if($item->description)
                        <div class="mt-3 p-2 rounded-2" style="background:var(--bg-muted);font-size:12.5px;color:var(--text-secondary)">
                            <i class="bi bi-chat-left-text me-1"></i> {{ $item->description }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Total Stok --}}
            <div class="card border-0 animate-in" style="background:var(--accent-primary);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-body p-4">
                    <p class="mb-1 text-white opacity-75" style="font-size:12.5px">Total Stok Tersedia</p>
                    <h2 class="fw-bold text-white mb-0" id="totalStockDisplay">—</h2>
                    <p class="text-white opacity-75 mb-0 mt-1" style="font-size:12px">
                        <span id="totalBatchDisplay">—</span> batch aktif
                    </p>
                </div>
            </div>

        </div>

        {{-- Kolom Kanan: Tab Stok & Pergerakan --}}
        <div class="col-lg-8">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">

                {{-- Tab Navigation --}}
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <ul class="nav nav-tabs border-0 gap-1" id="itemTabs">
                        <li class="nav-item">
                            <button class="nav-link active fw-semibold px-3 py-2" data-bs-toggle="tab" data-bs-target="#tabStocks"
                                    style="font-size:13px;border-radius:var(--radius-md) var(--radius-md) 0 0">
                                <i class="bi bi-layers me-1"></i> Batch Stok
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-semibold px-3 py-2" data-bs-toggle="tab" data-bs-target="#tabMovements"
                                    style="font-size:13px;border-radius:var(--radius-md) var(--radius-md) 0 0">
                                <i class="bi bi-clock-history me-1"></i> Riwayat Pergerakan
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body px-4 pb-4 tab-content">

                    {{-- Tab: Batch Stok --}}
                    <div class="tab-pane fade show active pt-3" id="tabStocks">
                        <div id="stocksContainer">
                            <div class="text-center py-5 text-muted" id="stocksLoading">
                                <div class="spinner-border spinner-border-sm me-2"></div> Memuat data stok...
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Riwayat Pergerakan --}}
                    <div class="tab-pane fade pt-3" id="tabMovements">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 w-100" id="movementsTable" style="font-size:13px">
                                <thead>
                                    <tr>
                                        <th width="50">No</th>
                                        <th width="130">Tipe</th>
                                        <th width="80" class="text-center">Jumlah</th>
                                        <th width="160">Perubahan Stok</th>
                                        <th width="100">Batch</th>
                                        <th>Catatan</th>
                                        <th>Oleh</th>
                                        <th width="140">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@include('items._modal_form')
@endsection

@push('scripts')
@include('items._scripts_show', ['item' => $item])
@endpush
