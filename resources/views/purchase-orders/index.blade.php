@extends('layouts.app')

@section('title', 'Purchase Order')

@section('content')
    <div class="container-fluid px-4 py-4">

        {{-- Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
            <div>
                <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary)">Purchase Order</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="font-size: 12.5px">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-decoration-none" style="color: var(--text-muted)">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" style="color: var(--text-secondary)">Purchase Order</li>
                    </ol>
                </nav>
            </div>
            @can('create', App\Models\PurchaseOrder::class)
                <div class="d-flex gap-2">
                    <a href="{{ route('purchase-orders.trash') }}"
                       class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                       style="border-radius: var(--radius-md); font-size: 13px">
                        <i class="bi bi-trash"></i> Sampah
                    </a>
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-2"
                            id="btnCreatePO" style="border-radius: var(--radius-md); background: var(--accent-primary); border-color: var(--accent-primary)">
                        <i class="bi bi-plus-lg"></i> Buat PO Baru
                    </button>
                </div>
            @endcan
        </div>

        {{-- Kartu Statistik --}}
        <div class="row g-3 mb-4 animate-in">
            <div class="col-6 col-md-3 col-xl">
                <div class="card border-0 px-3 py-3" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-subtle)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon-wrap" style="background: rgba(79,70,229,0.1); color: var(--accent-primary)">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 12.5px; font-weight: 500">Total PO</span>
                            <h3 class="fw-bold mb-0" id="stat-total" data-count="0" style="color: var(--text-primary)">0</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 col-xl">
                <div class="card border-0 px-3 py-3" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-subtle)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon-wrap" style="background: rgba(100,116,139,0.1); color: #64748b">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 12.5px; font-weight: 500">Draft</span>
                            <h3 class="fw-bold mb-0" id="stat-draft" data-count="0" style="color: var(--text-primary)">0</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 col-xl">
                <div class="card border-0 px-3 py-3" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-subtle)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon-wrap" style="background: rgba(234,179,8,0.1); color: #eab308">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 12.5px; font-weight: 500">Dalam Proses</span>
                            <h3 class="fw-bold mb-0" id="stat-ordered" data-count="0" style="color: var(--text-primary)">0</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 col-xl">
                <div class="card border-0 px-3 py-3" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-subtle)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon-wrap" style="background: rgba(34,197,94,0.1); color: #22c55e">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 12.5px; font-weight: 500">Diterima</span>
                            <h3 class="fw-bold mb-0" id="stat-received" data-count="0" style="color: var(--text-primary)">0</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 col-xl">
                <div class="card border-0 px-3 py-3" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-subtle)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon-wrap" style="background: rgba(239,68,68,0.1); color: #ef4444">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 12.5px; font-weight: 500">Dibatalkan</span>
                            <h3 class="fw-bold mb-0" id="stat-cancelled" data-count="0" style="color: var(--text-primary)">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Utama --}}
        <div class="card border-0 animate-in" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm)">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1" style="color: var(--text-primary)">Daftar Purchase Order</h5>
                    <p class="text-muted mb-0" style="font-size: 12.5px">Kelola pengadaan barang sarana & prasarana.</p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- Filter Status --}}
                    <select id="filterStatus" class="form-select form-select-sm" style="width: auto; border-radius: 50px; font-size: 13px;">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="approved">Disetujui</option>
                        <option value="ordered">Dipesan</option>
                        <option value="received">Diterima</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                    {{-- Search --}}
                    <div class="position-relative">
                        <i class="bi bi-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:13px;"></i>
                        <input type="text" id="tableSearch" class="form-control form-control-sm ps-5"
                               placeholder="Cari nomor PO, supplier..." style="width: 230px; border-radius: 50px; font-size: 13px;">
                    </div>
                </div>
            </div>

            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 w-100" id="poTable">
                        <thead>
                            <tr>
                                <th width="50px" class="text-center">No</th>
                                <th width="160px">No. PO</th>
                                <th>Supplier</th>
                                <th width="130px">Tgl. Order</th>
                                <th width="160px" class="text-end">Total</th>
                                <th width="130px" class="text-center">Status</th>
                                <th width="60px">Dibuat Oleh</th>
                                <th width="140px" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Include modals --}}
    @include('purchase-orders._modal_form')
    @include('purchase-orders._modal_receive')
@endsection

@push('scripts')
    @include('purchase-orders._scripts_index')
@endpush
