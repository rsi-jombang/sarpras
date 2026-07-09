@extends('layouts.app')

@section('title', 'Ajukan Permintaan Barang')

@section('content')
@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            font-size: 13px;
        }
    </style>
@endpush

<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">Ajukan Permintaan Barang</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:12.5px">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none" style="color:var(--text-muted)">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('requests.index') }}" class="text-decoration-none" style="color:var(--text-muted)">Permintaan</a></li>
                    <li class="breadcrumb-item active" style="color:var(--text-secondary)">Ajukan Baru</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1"
           style="border-radius:var(--radius-md);font-size:13px">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Info Unit --}}
    <div class="alert border-0 d-flex align-items-center gap-3 mb-4 animate-in"
         style="background:rgba(79,70,229,0.08);border-left:4px solid var(--accent-primary)!important;border-radius:var(--radius-md)">
        <i class="bi bi-building" style="font-size:1.1rem;color:var(--accent-primary);flex-shrink:0"></i>
        <div style="font-size:13px">
            <strong style="color:var(--text-primary)">{{ auth()->user()->name }}</strong>
            <span class="text-muted"> — {{ auth()->user()->unit->name ?? 'Unit tidak diketahui' }}</span>
        </div>
    </div>

    <form action="{{ route('requests.store') }}" method="POST" id="createRequestForm" novalidate>
        @csrf

        <div class="row g-4">
            {{-- Kolom Kiri: Info Umum --}}
            <div class="col-lg-4">
                <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3" style="color:var(--text-secondary);font-size:12px;text-transform:uppercase;letter-spacing:.5px">
                            <i class="bi bi-info-circle me-2"></i>Informasi Pengajuan
                        </h6>

                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:500">
                                Prioritas <span class="text-danger">*</span>
                            </label>
                            <select name="priority" class="form-select form-select-sm @error('priority') is-invalid @enderror"
                                    style="font-size:13px" required>
                                <option value="low"    {{ old('priority') === 'low'    ? 'selected' : '' }}>Rendah</option>
                                <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high"   {{ old('priority') === 'high'   ? 'selected' : '' }}>Tinggi</option>
                                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Mendesak</option>
                            </select>
                            @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:500">Tanggal Dibutuhkan</label>
                            <input type="date" name="needed_date"
                                   class="form-control form-control-sm @error('needed_date') is-invalid @enderror"
                                   value="{{ old('needed_date') }}" min="{{ date('Y-m-d') }}" style="font-size:13px">
                            @error('needed_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:500">Keperluan / Tujuan</label>
                            <textarea name="purpose" rows="3"
                                      class="form-control form-control-sm @error('purpose') is-invalid @enderror"
                                      placeholder="Contoh: Untuk keperluan pasien rawat inap..."
                                      style="font-size:13px">{{ old('purpose') }}</textarea>
                            @error('purpose')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label" style="font-size:13px;font-weight:500">Catatan Tambahan</label>
                            <textarea name="notes" rows="2"
                                      class="form-control form-control-sm @error('notes') is-invalid @enderror"
                                      placeholder="Keterangan lain jika diperlukan..."
                                      style="font-size:13px">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Daftar Barang --}}
            <div class="col-lg-8">
                <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0" style="color:var(--text-secondary);font-size:12px;text-transform:uppercase;letter-spacing:.5px">
                                <i class="bi bi-box-seam me-2"></i>Barang yang Diminta
                            </h6>
                            <button type="button" id="btnAddItem"
                                    class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                                    style="border-radius:var(--radius-sm);font-size:12.5px">
                                <i class="bi bi-plus-lg"></i> Tambah Barang
                            </button>
                        </div>

                        @error('items')
                            <div class="alert alert-danger py-2 mb-3" style="font-size:13px">{{ $message }}</div>
                        @enderror

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0" style="font-size:13px">
                                <thead>
                                    <tr style="background:var(--bg-muted)">
                                        <th style="width:40%">Barang</th>
                                        <th style="width:15%" class="text-center">Stok</th>
                                        <th style="width:15%" class="text-center">Jumlah <span class="text-danger">*</span></th>
                                        <th style="width:10%" class="text-center">Satuan</th>
                                        <th style="width:15%">Catatan</th>
                                        <th style="width:5%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody"></tbody>
                            </table>
                        </div>

                        <div id="emptyMsg" class="text-center py-5 text-muted" style="font-size:13px">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                            Belum ada barang. Klik <strong>"Tambah Barang"</strong> untuk mulai.
                        </div>

                    </div>
                </div>

                {{-- Tombol Submit --}}
                <div class="d-flex justify-content-end gap-2 mt-3 animate-in">
                    <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary"
                       style="border-radius:var(--radius-md);font-size:13px">Batal</a>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                            id="btnSubmit"
                            style="border-radius:var(--radius-md);background:var(--accent-primary);border-color:var(--accent-primary);font-size:13px">
                        <span class="spinner-border spinner-border-sm d-none" id="btnSubmitSpinner"></span>
                        <i class="bi bi-save"></i> Simpan Draft
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

{{-- Template Baris Item --}}
<template id="itemRowTpl">
    <tr class="item-row" data-index="__INDEX__">
        <td>
            <select class="form-select form-select-sm item-select"
                    name="items[__INDEX__][item_id]" style="font-size:13px;min-width:200px" required>
                <option value="">-- Pilih Barang --</option>
            </select>
            @if($errors->has('items.__INDEX__.item_id'))
                <div class="text-danger" style="font-size:11.5px">{{ $errors->first('items.__INDEX__.item_id') }}</div>
            @endif
        </td>
        <td class="text-center">
            <span class="item-stock text-muted fw-semibold" style="font-size:13px">—</span>
        </td>
        <td class="text-center">
            <input type="number" class="form-control form-control-sm text-center item-qty"
                   name="items[__INDEX__][quantity_requested]"
                   min="1" value="1" style="font-size:13px;width:70px;margin:0 auto" required>
        </td>
        <td class="text-center">
            <span class="item-uom text-muted" style="font-size:12px">—</span>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm item-note"
                   name="items[__INDEX__][notes]"
                   placeholder="Keterangan..." style="font-size:12px">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item"
                    style="border-radius:var(--radius-sm);width:30px;height:30px;padding:0">
                <i class="bi bi-trash" style="font-size:12px"></i>
            </button>
        </td>
    </tr>
</template>
@endsection

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@include('requests._scripts_create')
@endpush
