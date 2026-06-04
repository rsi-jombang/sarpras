@extends('layouts.app')

@section('title', 'Detail Opname - ' . $stockOpname->opname_number)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">{{ $stockOpname->opname_number }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:12.5px">
                    <li class="breadcrumb-item"><a href="{{ route('stock-opname.index') }}" class="text-decoration-none" style="color:var(--text-muted)">Stok Opname</a></li>
                    <li class="breadcrumb-item active" style="color:var(--text-secondary)">{{ $stockOpname->opname_number }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if($stockOpname->isOpen())
                @can('close', $stockOpname)
                    <a href="{{ route('stock-opname.close.confirm', $stockOpname) }}"
                       class="btn btn-success d-flex align-items-center gap-1"
                       style="border-radius:var(--radius-md);font-size:13px">
                        <i class="bi bi-lock"></i> Tutup Opname
                    </a>
                @endcan
            @endif
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert border-0 d-flex align-items-center gap-2 mb-4"
             style="background:rgba(34,197,94,0.1);border-left:4px solid #22c55e!important;border-radius:var(--radius-md);font-size:13px">
            <i class="bi bi-check-circle text-success"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert border-0 d-flex align-items-center gap-2 mb-4"
             style="background:rgba(239,68,68,0.1);border-left:4px solid #ef4444!important;border-radius:var(--radius-md);font-size:13px">
            <i class="bi bi-exclamation-circle text-danger"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">

        {{-- Kolom Kiri: Info & Progress --}}
        <div class="col-lg-3">

            {{-- Status & Info --}}
            <div class="card border-0 mb-4 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-body p-4">
                    @php
                        $statusMap = [
                            'open'        => ['warning', 'Terbuka'],
                            'in_progress' => ['info',    'Sedang Berjalan'],
                            'closed'      => ['success', 'Ditutup'],
                        ];
                        [$sColor, $sLabel] = $statusMap[$stockOpname->status] ?? ['secondary', '—'];
                    @endphp
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0" style="color:var(--text-secondary);font-size:12px;text-transform:uppercase;letter-spacing:.5px">Status</h6>
                        <span class="badge rounded-pill bg-{{ $sColor }}-subtle text-{{ $sColor }} px-3 py-2" style="font-size:12.5px">{{ $sLabel }}</span>
                    </div>

                    @foreach([
                        ['bi-hash',         'No. Opname',    $stockOpname->opname_number],
                        ['bi-calendar3',    'Tgl. Opname',   $stockOpname->opname_date->format('d M Y')],
                        ['bi-person',       'Dibuat Oleh',   $stockOpname->creator->name ?? '—'],
                        ['bi-person-check', 'Ditutup Oleh',  $stockOpname->closer?->name ?? '—'],
                        ['bi-calendar-x',   'Tgl. Tutup',    $stockOpname->closed_date?->format('d M Y') ?? '—'],
                    ] as [$icon, $label, $value])
                        <div class="d-flex gap-2 mb-2 align-items-start" style="font-size:12.5px">
                            <i class="bi {{ $icon }} text-muted mt-1" style="width:14px;flex-shrink:0"></i>
                            <span class="text-muted" style="min-width:90px;flex-shrink:0">{{ $label }}</span>
                            <span class="fw-medium" style="color:var(--text-primary)">{{ $value }}</span>
                        </div>
                    @endforeach

                    @if($stockOpname->notes)
                        <div class="mt-3 p-2 rounded-2" style="background:var(--bg-muted);font-size:12px;color:var(--text-secondary)">
                            <i class="bi bi-sticky me-1"></i>{{ $stockOpname->notes }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Progress --}}
            <div class="card border-0 mb-4 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3" style="color:var(--text-secondary);font-size:12px;text-transform:uppercase;letter-spacing:.5px">Progress Hitung</h6>

                    <div class="text-center mb-3">
                        <h2 class="fw-bold mb-0" style="color:var(--accent-primary)">{{ $progress }}%</h2>
                        <p class="text-muted mb-0" style="font-size:12.5px">{{ $countedItems }} dari {{ $totalItems }} item</p>
                    </div>

                    <div class="progress mb-4" style="height:8px;border-radius:99px">
                        <div class="progress-bar {{ $progress === 100 ? 'bg-success' : 'bg-primary' }}"
                             style="width:{{ $progress }}%;background:var(--accent-primary)!important"></div>
                    </div>

                    <div class="row g-2 text-center" style="font-size:12.5px">
                        <div class="col-4">
                            <div class="fw-bold" style="color:var(--text-primary)">{{ $totalItems }}</div>
                            <div class="text-muted">Total</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-success">{{ $countedItems }}</div>
                            <div class="text-muted">Dihitung</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-{{ $diffItems > 0 ? 'warning' : 'success' }}">{{ $diffItems }}</div>
                            <div class="text-muted">Selisih</div>
                        </div>
                    </div>

                    @if($stockOpname->isOpen() && $progress === 100)
                        <div class="mt-3 p-2 rounded-2 text-center" style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);font-size:12.5px">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            <span class="text-success fw-semibold">Semua item sudah dihitung!</span><br>
                            <span class="text-muted">Opname siap untuk ditutup.</span>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Kolom Kanan: Tabel Item --}}
        <div class="col-lg-9">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">

                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex flex-column flex-sm-row justify-content-between gap-3">
                    <div>
                        <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Daftar Item Opname</h5>
                        <p class="text-muted mb-0" style="font-size:12.5px">
                            @if($stockOpname->isOpen())
                                Centang item dan isi jumlah fisik, lalu klik <strong>Simpan</strong>.
                            @else
                                Data opname telah ditutup. Hanya bisa dilihat.
                            @endif
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <select id="filterCountStatus" class="form-select form-select-sm" style="width:auto;border-radius:50px;font-size:13px">
                            <option value="">Semua Item</option>
                            <option value="counted">Sudah Dihitung</option>
                            <option value="uncounted">Belum Dihitung</option>
                            <option value="diff">Ada Selisih</option>
                        </select>
                        <div class="position-relative">
                            <i class="bi bi-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px"></i>
                            <input type="text" id="itemSearch" class="form-control form-control-sm ps-5"
                                   placeholder="Cari barang..." style="width:200px;border-radius:50px;font-size:13px">
                        </div>
                    </div>
                </div>

                {{-- Form input hasil hitung (hanya jika masih open) --}}
                @if($stockOpname->isOpen())
                    <form action="{{ route('stock-opname.update-items', $stockOpname) }}" method="POST" id="opnameForm">
                        @csrf
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 w-100" id="opnameItemsTable" style="font-size:13px">
                                    <thead>
                                        <tr>
                                            <th width="50" class="text-center">No</th>
                                            <th>Barang</th>
                                            <th width="70" class="text-center">Satuan</th>
                                            <th width="90">Batch</th>
                                            <th width="100" class="text-center">Stok Sistem</th>
                                            <th width="110" class="text-center">Fisik</th>
                                            <th width="90" class="text-center">Selisih</th>
                                        </tr>
                                    </thead>
                                    <tbody id="opnameItemsBody"></tbody>
                                </table>
                            </div>

                            <div id="emptyOpnameMsg" class="text-center py-5 text-muted d-none" style="font-size:13px">
                                <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                Tidak ada item yang cocok dengan filter.
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3"
                                 style="border-top:1px solid var(--border-subtle)">
                                <span class="text-muted" style="font-size:12.5px" id="selectedCount">0 item dipilih untuk disimpan</span>
                                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                        id="btnSaveItems" disabled
                                        style="border-radius:var(--radius-md);background:var(--accent-primary);border-color:var(--accent-primary);font-size:13px">
                                    <span class="spinner-border spinner-border-sm d-none" id="btnSaveSpinner"></span>
                                    <i class="bi bi-floppy"></i> Simpan Hasil Hitung
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="card-body px-4 pb-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 w-100" id="opnameItemsTable" style="font-size:13px">
                                <thead>
                                    <tr>
                                        <th width="50" class="text-center">No</th>
                                        <th>Barang</th>
                                        <th width="70" class="text-center">Satuan</th>
                                        <th width="90">Batch</th>
                                        <th width="100" class="text-center">Stok Sistem</th>
                                        <th width="110" class="text-center">Fisik</th>
                                        <th width="90" class="text-center">Selisih</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('stock-opname._scripts_show', ['stockOpname' => $stockOpname])
@endpush
