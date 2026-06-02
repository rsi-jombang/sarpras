@extends('layouts.app')

@section('title', 'Sampah Barang')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">Sampah Barang</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:12.5px">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none" style="color:var(--text-muted)">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}" class="text-decoration-none" style="color:var(--text-muted)">Barang</a></li>
                    <li class="breadcrumb-item active" style="color:var(--text-secondary)">Sampah</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1"
           style="border-radius:var(--radius-md);font-size:13px">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Barang
        </a>
    </div>

    {{-- Info Banner --}}
    <div class="alert border-0 d-flex align-items-start gap-3 mb-4 animate-in"
         style="background:rgba(234,179,8,0.1);border-left:4px solid #eab308!important;border-radius:var(--radius-md)">
        <i class="bi bi-exclamation-triangle text-warning mt-1" style="font-size:1.1rem;flex-shrink:0"></i>
        <div style="font-size:13px">
            <strong style="color:var(--text-primary)">Data di halaman ini telah dihapus sementara.</strong><br>
            <span class="text-muted">Anda dapat memulihkan barang ke daftar aktif, atau menghapus secara permanen.
            Hapus permanen tidak dapat dibatalkan.</span>
        </div>
    </div>

    {{-- Tabel Trash --}}
    <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Barang Terhapus</h5>
                <p class="text-muted mb-0" style="font-size:12.5px">Total: <span id="trashCount" class="fw-semibold">0</span> barang</p>
            </div>
            <div class="position-relative">
                <i class="bi bi-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px"></i>
                <input type="text" id="trashSearch" class="form-control form-control-sm ps-5"
                       placeholder="Cari barang..." style="width:220px;border-radius:50px;font-size:13px">
            </div>
        </div>
        <div class="card-body px-4 pb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100" id="trashTable" style="font-size:13px">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th width="130" class="text-center">Tipe</th>
                            <th width="160">Dihapus Pada</th>
                            <th width="130" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
@include('items._scripts_trash')
@endpush
