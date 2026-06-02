@extends('layouts.app')

@section('title', 'Manajemen Barang')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">Manajemen Barang</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:12.5px">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none" style="color:var(--text-muted)">Dashboard</a></li>
                    <li class="breadcrumb-item active" style="color:var(--text-secondary)">Barang</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @can('create', App\Models\Item::class)
                <a href="{{ route('items.trash') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                   style="border-radius:var(--radius-md);font-size:13px">
                    <i class="bi bi-trash"></i> Sampah
                </a>
                <button type="button" id="btnCreateItem" class="btn btn-primary d-flex align-items-center gap-2"
                        style="border-radius:var(--radius-md);background:var(--accent-primary);border-color:var(--accent-primary)">
                    <i class="bi bi-plus-lg"></i> Tambah Barang
                </button>
            @endcan
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4 animate-in">
        @foreach([
            ['total',       'bi-box-seam',          'var(--accent-primary)', 'rgba(79,70,229,0.1)',  'Total Barang'],
            ['consumable',  'bi-capsule',            '#0ea5e9',               'rgba(14,165,233,0.1)', 'Habis Pakai'],
            ['inventory',   'bi-tools',              '#8b5cf6',               'rgba(139,92,246,0.1)', 'Inventaris'],
            ['low_stock',   'bi-exclamation-triangle','#eab308',              'rgba(234,179,8,0.1)',  'Stok Rendah'],
            ['empty_stock', 'bi-x-circle',           '#ef4444',               'rgba(239,68,68,0.1)',  'Stok Habis'],
        ] as [$key, $icon, $color, $bg, $label])
        <div class="col-6 col-md-4 col-xl">
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

    {{-- Tabel Utama --}}
    <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
            <div>
                <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Daftar Barang</h5>
                <p class="text-muted mb-0" style="font-size:12.5px">Kelola master data barang sarana & prasarana.</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select id="filterCategory" class="form-select form-select-sm" style="width:auto;border-radius:50px;font-size:13px">
                    <option value="">Semua Kategori</option>
                </select>
                <select id="filterType" class="form-select form-select-sm" style="width:auto;border-radius:50px;font-size:13px">
                    <option value="">Semua Tipe</option>
                    <option value="consumable">Habis Pakai</option>
                    <option value="inventory">Inventaris</option>
                </select>
                <select id="filterStock" class="form-select form-select-sm" style="width:auto;border-radius:50px;font-size:13px">
                    <option value="">Semua Stok</option>
                    <option value="low">Stok Rendah</option>
                    <option value="empty">Stok Habis</option>
                </select>
                <div class="position-relative">
                    <i class="bi bi-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px"></i>
                    <input type="text" id="tableSearch" class="form-control form-control-sm ps-5"
                           placeholder="Cari nama, kode..." style="width:220px;border-radius:50px;font-size:13px">
                </div>
            </div>
        </div>
        <div class="card-body px-4 pb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100" id="itemTable">
                    <thead>
                        <tr>
                            <th width="50px" class="text-center">No</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th width="100px">Satuan</th>
                            <th width="160px" class="text-center">Total Stok</th>
                            <th width="140px" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@include('items._modal_form')
@endsection

@push('scripts')
@include('items._scripts_index')
@endpush
