@extends('reports.pdf._layout')

@section('title', 'Laporan Purchase Order')

@section('content')

    <div class="meta-grid">
        @if (!empty($filters['status']))
            <div class="meta-item">
                <div class="label">Status</div>
                <div class="value">{{ ucfirst($filters['status']) }}</div>
            </div>
        @endif
        @if (!empty($filters['date_from']) || !empty($filters['date_to']))
            <div class="meta-item">
                <div class="label">Periode Order</div>
                <div class="value">
                    {{ !empty($filters['date_from']) ? \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') : '—' }}
                    s/d
                    {{ !empty($filters['date_to']) ? \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') : 'sekarang' }}
                </div>
            </div>
        @endif
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <div class="s-value">{{ $summary['total'] }}</div>
            <div class="s-label">Total PO</div>
        </div>
        <div class="summary-card">
            <div class="s-value" style="color:#22c55e">{{ $summary['received'] }}</div>
            <div class="s-label">Diterima</div>
        </div>
        <div class="summary-card">
            <div class="s-value" style="color:#ef4444">{{ $summary['cancelled'] }}</div>
            <div class="s-label">Dibatalkan</div>
        </div>
        <div class="summary-card">
            <div class="s-value" style="color:#4f46e5; font-size:14px">
                Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}
            </div>
            <div class="s-label">Total Nilai Diterima</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. PO</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Tgl. Order</th>
                <th>Est. Terima</th>
                <th>Tgl. Diterima</th>
                <th class="text-right">Total (Rp)</th>
                <th>Dibuat Oleh</th>
                <th>Disetujui Oleh</th>
            </tr>
        </thead>
        <tbody>
            @php
                $statusMap = [
                    'draft' => ['secondary', 'Draft'],
                    'approved' => ['info', 'Disetujui'],
                    'ordered' => ['warning', 'Dipesan'],
                    'received' => ['success', 'Diterima'],
                    'cancelled' => ['danger', 'Dibatalkan'],
                ];
            @endphp
            @forelse($purchaseOrders as $i => $po)
                @php [$bColor, $bLabel] = $statusMap[$po->status->value] ?? ['secondary', $po->status->value]; @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="fw-bold text-primary">{{ $po->po_number }}</td>
                    <td>{{ $po->supplier_name ?? '—' }}</td>
                    <td><span class="badge badge-{{ $bColor }}">{{ $bLabel }}</span></td>
                    <td>{{ $po->order_date?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $po->expected_date?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $po->received_date?->format('d/m/Y') ?? '—' }}</td>
                    <td class="text-right fw-bold">{{ number_format($po->total_amount, 0, ',', '.') }}</td>
                    <td>{{ $po->creator->name ?? '—' }}</td>
                    <td>{{ $po->approver->name ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted">Tidak ada data purchase order.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-right">Total Nilai:</td>
                <td class="text-right">{{ number_format($purchaseOrders->sum('total_amount'), 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
@endsection
