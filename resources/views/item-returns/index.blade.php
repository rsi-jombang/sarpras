@extends('layouts.app')

@section('title', 'Pengembalian Barang')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">Pengembalian Barang</h1>
            <p class="text-muted mb-0" style="font-size:13px">Daftar pengembalian barang ke Sarpras.</p>
        </div>
        @can('create', \App\Models\ItemReturn::class)
            <a href="{{ route('item-returns.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i> Buat Pengembalian
            </a>
        @endcan
    </div>

    <div class="card border-0">
        <div class="card-body px-4 pb-4">
            <table class="table table-hover align-middle w-100" id="returnTable" style="font-size:13px">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Retur</th>
                        <th>Unit</th>
                        <th>Pengaju</th>
                        <th>Status</th>
                        <th>Jml Item</th>
                        <th>Tgl</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('item-returns._scripts_index')
@endpush