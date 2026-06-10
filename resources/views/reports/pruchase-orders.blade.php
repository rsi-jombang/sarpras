@extends('layouts.app')

@section('title', 'Laporan Purchase Order')

@section('content')
    <div class="container-fluid px-4 py-4">

        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
            <div>
                <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">Laporan Purchase Order</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="font-size:12.5px">
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}" class="text-decoration-none"
                                style="color:var(--text-muted)">Laporan</a></li>
                        <li class="breadcrumb-item active" style="color:var(--text-secondary)">Purchase Order</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.purchase-orders.pdf', request()->query()) }}"
                    class="btn btn-danger d-flex align-items-center gap-1"
                    style="border-radius:var(--radius-md);font-size:13px" target="_blank">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
                <a href="{{ route('reports.purchase-orders.excel', request()->query()) }}"
                    class="btn btn-success d-flex align-items-center gap-1"
                    style="border-radius:var(--radius-md);font-size:13px">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
            </div>
        </div>

        {{-- Filter --}}
        <div class="card border-0 mb-4 animate-in"
            style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('reports.purchase-orders') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:13px;font-weight:500">Status</label>
                        <select name="status" class="form-select form-select-sm" style="font-size:13px">
                            <option value="">Semua Status</option>
                            @foreach (['draft' => 'Draft', 'approved' => 'Disetujui', 'ordered' => 'Dipesan', 'received' => 'Diterima', 'cancelled' => 'Dibatalkan'] as $val => $lbl)
                                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                                    {{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:13px;font-weight:500">Dari Tgl. Order</label>
                        <input type="date" name="date_from" class="form-control form-control-sm"
                            value="{{ request('date_from') }}" style="font-size:13px">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:13px;font-weight:500">Sampai Tgl. Order</label>
                        <input type="date" name="date_to" class="form-control form-control-sm"
                            value="{{ request('date_to') }}" style="font-size:13px">
                    </div>
                    <div class="col-md-3 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm"
                            style="border-radius:var(--radius-md);font-size:13px">
                            <i class="bi bi-search me-1"></i>Terapkan
                        </button>
                        <a href="{{ route('reports.purchase-orders') }}" class="btn btn-outline-secondary btn-sm"
                            style="border-radius:var(--radius-md);font-size:13px">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary --}}
        <div class="row g-3 mb-4 animate-in">
            @foreach ([[$summary['total'], 'Total PO', 'var(--accent-primary)', 'rgba(79,70,229,0.1)'], [$summary['received'], 'Diterima', '#22c55e', 'rgba(34,197,94,0.1)'], [$summary['cancelled'], 'Dibatalkan', '#ef4444', 'rgba(239,68,68,0.1)'], ['Rp ' . number_format($summary['total_amount'], 0, ',', '.'), 'Nilai Diterima', '#8b5cf6', 'rgba(139,92,246,0.1)']] as [$val, $lbl, $color, $bg])
                <div class="col-6 col-md-3">
                    <div class="card border-0 px-3 py-3"
                        style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);border:1px solid var(--border-subtle)!important">
                        <h4 class="fw-bold mb-0" style="color:{{ $color }};font-size:1.1rem">{{ $val }}
                        </h4>
                        <p class="text-muted mb-0" style="font-size:12.5px">{{ $lbl }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Tabel --}}
        <div class="card border-0 animate-in"
            style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Preview Data</h5>
                <p class="text-muted mb-0" style="font-size:12.5px">Menampilkan {{ $purchaseOrders->count() }} dari
                    {{ $purchaseOrders->total() }} PO.</p>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" style="font-size:13px">
                        <thead>
                            <tr>
                                <th>No. PO</th>
                                <th>Supplier</th>
                                <th class="text-center">Status</th>
                                <th>Tgl. Order</th>
                                <th>Tgl. Diterima</th>
                                <th class="text-end">Total (Rp)</th>
                                <th>Dibuat Oleh</th>
                                <th>Disetujui Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $statusMap = ['draft'=>['secondary','Draft'],'approved'=>['info','Disetujui'],'ordered'=>['warning','Dipesan'],'received'=>['success','Diterima'],'cancelled'=>['danger','Dibatalkan']]; @endphp
                            @forelse($purchaseOrders as $po)
                                @php [$sColor, $sLabel] = $statusMap[$po->status->value] ?? ['secondary', $po->status->value]; @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('purchase-orders.show', $po) }}"
                                            class="fw-bold text-decoration-none" style="color:var(--accent-primary)">
                                            {{ $po->po_number }}
                                        </a>
                                    </td>
                                    <td>{{ $po->supplier_name ?? '—' }}</td>
                                    <td class="text-center">
                                        <span
                                            class="badge rounded-pill bg-{{ $sColor }}-subtle text-{{ $sColor }}"
                                            style="font-size:10px">{{ $sLabel }}</span>
                                    </td>
                                    <td>{{ $po->order_date?->format('d M Y') ?? '—' }}</td>
                                    <td>{{ $po->received_date?->format('d M Y') ?? '—' }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($po->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td>{{ $po->creator->name ?? '—' }}</td>
                                    <td>{{ $po->approver->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox d-block mb-2 fs-2 opacity-50"></i>
                                        Tidak ada data yang cocok.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($purchaseOrders->count() > 0)
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end fw-bold" style="color:var(--text-secondary)">Total
                                        Nilai:</td>
                                    <td class="text-end fw-bold" style="color:var(--accent-primary)">
                                        {{ number_format($purchaseOrders->sum('total_amount'), 0, ',', '.') }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
                {{ $purchaseOrders->withQueryString()->links() }}
            </div>
        </div>

    </div>
@endsection
