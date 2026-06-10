@extends('reports.pdf._layout')

@section('title', 'Laporan Permintaan Barang')

@section('content')

    {{-- Filter Info --}}
    <div class="meta-grid">
        @if (!empty($filters['unit_id']))
            <div class="meta-item">
                <div class="label">Unit</div>
                <div class="value">{{ \App\Models\Unit::find($filters['unit_id'])?->name ?? '—' }}</div>
            </div>
        @endif
        @if (!empty($filters['status']))
            <div class="meta-item">
                <div class="label">Status</div>
                <div class="value">{{ ucfirst($filters['status']) }}</div>
            </div>
        @endif
        @if (!empty($filters['date_from']) || !empty($filters['date_to']))
            <div class="meta-item">
                <div class="label">Periode</div>
                <div class="value">
                    {{ !empty($filters['date_from']) ? \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') : '—' }}
                    s/d
                    {{ !empty($filters['date_to']) ? \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') : 'sekarang' }}
                </div>
            </div>
        @endif
    </div>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <div class="summary-card">
            <div class="s-value">{{ $summary['total'] }}</div>
            <div class="s-label">Total Permintaan</div>
        </div>
        <div class="summary-card">
            <div class="s-value" style="color:#22c55e">{{ $summary['completed'] }}</div>
            <div class="s-label">Selesai</div>
        </div>
        <div class="summary-card">
            <div class="s-value" style="color:#ef4444">{{ $summary['rejected'] }}</div>
            <div class="s-label">Ditolak</div>
        </div>
        <div class="summary-card">
            <div class="s-value" style="color:#eab308">{{ $summary['pending'] }}</div>
            <div class="s-label">Masih Pending</div>
        </div>
    </div>

    {{-- Tabel --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Permintaan</th>
                <th>Unit</th>
                <th>Pengaju</th>
                <th>Prioritas</th>
                <th>Status</th>
                <th>Tgl. Ajuan</th>
                <th>Tgl. Butuh</th>
                <th class="text-center">Item</th>
                <th>Direview Oleh</th>
                <th>Diambil Oleh</th>
            </tr>
        </thead>
        <tbody>
            @php
                $statusMap = [
                    'pending' => ['warning', 'Menunggu'],
                    'approved' => ['info', 'Disetujui'],
                    'partial' => ['info', 'Sebagian'],
                    'rejected' => ['danger', 'Ditolak'],
                    'ready' => ['info', 'Siap Ambil'],
                    'completed' => ['success', 'Selesai'],
                    'cancelled' => ['secondary', 'Dibatalkan'],
                ];
                $priorityMap = ['low' => 'Rendah', 'normal' => 'Normal', 'high' => 'Tinggi', 'urgent' => 'Mendesak'];
            @endphp
            @forelse($requests as $i => $req)
                @php [$bColor, $bLabel] = $statusMap[$req->status->value] ?? ['secondary', $req->status->value]; @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="fw-bold text-primary">{{ $req->request_number }}</td>
                    <td>{{ $req->unit->name ?? '—' }}</td>
                    <td>{{ $req->requester->name ?? '—' }}</td>
                    <td>{{ $priorityMap[$req->priority] ?? $req->priority }}</td>
                    <td><span class="badge badge-{{ $bColor }}">{{ $bLabel }}</span></td>
                    <td>{{ $req->request_date->format('d/m/Y') }}</td>
                    <td>{{ $req->needed_date?->format('d/m/Y') ?? '—' }}</td>
                    <td class="text-center">{{ $req->items->count() }}</td>
                    <td>{{ $req->reviewer->name ?? '—' }}</td>
                    <td>{{ $req->received_by_name ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center text-muted">Tidak ada data permintaan.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" class="text-right">Total Permintaan:</td>
                <td class="text-center">{{ $requests->count() }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
@endsection
