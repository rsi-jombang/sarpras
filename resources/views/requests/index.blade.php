@extends('layouts.app')

@section('title', 'Permintaan Barang')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">Permintaan Barang</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:12.5px">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none" style="color:var(--text-muted)">Dashboard</a></li>
                    <li class="breadcrumb-item active" style="color:var(--text-secondary)">Permintaan</li>
                </ol>
            </nav>
        </div>
        @can('create', App\Models\Request::class)
            <a href="{{ route('requests.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
               style="border-radius:var(--radius-md);background:var(--accent-primary);border-color:var(--accent-primary)">
                <i class="bi bi-plus-lg"></i> Ajukan Permintaan
            </a>
        @endcan
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4 animate-in">
        @foreach([
            ['total',     'bi-clipboard-list',  'var(--accent-primary)', 'rgba(79,70,229,0.1)',  'Total'],
            ['draft',     'bi-file-earmark',    '#6b7280',               'rgba(107,114,128,0.1)','Draft'],
            ['pending',   'bi-hourglass-split', '#eab308',               'rgba(234,179,8,0.1)',  'Menunggu Review'],
            ['approved',  'bi-check-circle',    '#0ea5e9',               'rgba(14,165,233,0.1)', 'Disetujui'],
            ['ready',     'bi-box-seam',        '#8b5cf6',               'rgba(139,92,246,0.1)', 'Siap Diambil'],
            ['completed', 'bi-bag-check',       '#22c55e',               'rgba(34,197,94,0.1)',  'Selesai (Bulan Ini)'],
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

    {{-- Tabel --}}
    <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
            <div>
                <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Daftar Permintaan</h5>
                <p class="text-muted mb-0" style="font-size:12.5px">
                    @auth
                        @if(auth()->user()->isUnit())
                            Permintaan yang diajukan oleh unit {{ auth()->user()->unit->name ?? '' }}.
                        @else
                            Semua permintaan dari seluruh unit.
                        @endif
                    @endauth
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                {{-- Filter Unit — hanya sarpras/admin --}}
                @if(!auth()->user()->isUnit())
                    <select id="filterUnit" class="form-select form-select-sm" style="width:auto;border-radius:50px;font-size:13px">
                        <option value="">Semua Unit</option>
                    </select>
                @endif
                <select id="filterStatus" class="form-select form-select-sm" style="width:auto;border-radius:50px;font-size:13px">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="pending">Menunggu</option>
                    <option value="approved">Disetujui</option>
                    <option value="partial">Sebagian</option>
                    <option value="rejected">Ditolak</option>
                    <option value="ready">Siap Diambil</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
                <input type="date" id="filterDateFrom" class="form-control form-control-sm"
                       style="width:auto;border-radius:50px;font-size:13px" title="Dari tanggal">
                <input type="date" id="filterDateTo" class="form-control form-control-sm"
                       style="width:auto;border-radius:50px;font-size:13px" title="Sampai tanggal">
                <div class="position-relative">
                    <i class="bi bi-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px"></i>
                    <input type="text" id="tableSearch" class="form-control form-control-sm ps-5"
                           placeholder="Cari nomor, pengaju..." style="width:220px;border-radius:50px;font-size:13px">
                </div>
            </div>
        </div>
        <div class="card-body px-4 pb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100" id="requestTable">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th width="160">No. Permintaan</th>
                            @if(!auth()->user()->isUnit())
                                <th>Unit</th>
                            @endif
                            <th>Pengaju</th>
                            <th width="100" class="text-center">Prioritas</th>
                            <th width="120" class="text-center">Status</th>
                            <th width="120">Tgl. Ajuan</th>
                            <th width="120">Tgl. Butuh</th>
                            <th width="150" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Modal Konfirmasi Pengambilan --}}
<div class="modal fade" id="completeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-0 pb-0" style="background:var(--bg-surface)">
                <h5 class="modal-title fw-bold" style="color:var(--text-primary)">Konfirmasi Pengambilan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="completeForm">
                <div class="modal-body px-4" style="background:var(--bg-surface)">
                    <input type="hidden" id="completeRequestId">
                    <p class="text-muted mb-3" style="font-size:13px">
                        Isi nama petugas yang mengambil barang untuk konfirmasi penerimaan.
                    </p>
                    <label class="form-label" style="font-size:13px;font-weight:500">
                        Nama Petugas Pengambil <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control form-control-sm" id="receivedByName"
                           name="received_by_name" placeholder="Nama lengkap petugas..." style="font-size:13px" required>
                    <div class="invalid-feedback" id="error-received_by_name"></div>
                </div>
                <div class="modal-footer border-0 pt-0" style="background:var(--bg-surface)">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                            style="border-radius:var(--radius-md);font-size:13px">Batal</button>
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-2"
                            style="border-radius:var(--radius-md);font-size:13px">
                        <span class="spinner-border spinner-border-sm d-none" id="btnCompleteSpinner"></span>
                        <i class="bi bi-check-lg"></i> Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('requests._scripts_index')
@endpush
