@extends('reports.pdf._layout')

@section('title', 'Laporan Hasil Stok Opname — ' . $opname->opname_number)

@section('content')

    <div class="meta-grid">
        <div class="meta-item">
            <div class="label">No. Opname</div>
            <div class="value">{{ $opname->opname_number }}</div>
        </div>
        <div class="meta-item">
            <div class="label">Tanggal Opname</div>
            <div class="value">{{ $opname->opname_date->format('d/m/Y') }}</div>
        </div>
        <div class="meta-item">
            <div class="label">Tanggal Ditutup</div>
            <div class="value">{{ $opname->closed_date?->format('d/m/Y') ?? '—' }}</div>
        </div>
        <div class="meta-item">
            <div class="label">Dibuat Oleh</div>
            <div class="value">{{ $opname->creator->name ?? '—' }}</div>
        </div>
        <div class="meta-item">
            <div class="label">Ditutup Oleh</div>
            <div class="value">{{ $opname->closer?->name ?? '—' }}</div>
        </div>
    </div>

    @php
        $totalItems = $opnameItems->count();
        $diffItems = $opnameItems->where('difference', '!=', 0)->whereNotNull('difference')->count();
        $surplusItems = $opnameItems->where('difference', '>', 0)->count();
        $deficitItems = $opnameItems->where('difference', '<', 0)->count();
        $matchItems = $opnameItems->where('difference', 0)->count();
    @endphp

    <div class="summary-cards">
        <div class="summary-card">
            <div class="s-value">{{ $totalItems }}</div>
            <div class="s-label">Total Item</div>
        </div>
        <div class="summary-card">
            <div class="s-value" style="color:#22c55e">{{ $matchItems }}</div>
            <div class="s-label">Sesuai</div>
        </div>
        <div class="summary-card">
            <div class="s-value" style="color:#0ea5e9">{{ $surplusItems }}</div>
            <div class="s-label">Surplus</div>
        </div>
        <div class="summary-card">
            <div class="s-value" style="color:#ef4444">{{ $deficitItems }}</div>
            <div class="s-label">Defisit</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th class="text-right">Stok Sistem</th>
                <th class="text-right">Stok Fisik</th>
                <th class="text-right">Selisih</th>
                <th>Keterangan</th>
                <th>Dihitung Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($opnameItems as $i => $oi)
                @php
                    $diff = $oi->difference;
                    $rowBg = match (true) {
                        $diff === null || $diff === 0 => '',
                        $diff > 0 => 'background:#f0fdf4',
                        default => 'background:#fef2f2',
                    };
                @endphp
                <tr style="{{ $rowBg }}">
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-muted">{{ $oi->item->code ?? '—' }}</td>
                    <td class="fw-bold">{{ $oi->item->name ?? '—' }}</td>
                    <td>{{ $oi->item->category->name ?? '—' }}</td>
                    <td>{{ $oi->item->unit_of_measure ?? '—' }}</td>
                    <td class="text-right">{{ $oi->system_quantity }}</td>
                    <td class="text-right">{{ $oi->physical_quantity ?? '—' }}</td>
                    <td class="text-right fw-bold {{ $diff > 0 ? 'text-success' : ($diff < 0 ? 'text-danger' : '') }}">
                        @if ($diff !== null)
                            {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if ($diff === null)
                            <span class="text-muted">—</span>
                        @elseif($diff === 0)
                            <span style="color:#16a34a">✓ Sesuai</span>
                        @elseif($diff > 0)
                            <span style="color:#0369a1">Surplus +{{ $diff }}</span>
                        @else
                            <span style="color:#dc2626">Defisit {{ $diff }}</span>
                        @endif
                    </td>
                    <td>{{ $oi->counter?->name ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9" class="text-right">Total item dengan selisih: <strong>{{ $diffItems }}</strong> dari
                    {{ $totalItems }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    @if ($opname->notes)
        <div
            style="margin-top:16px;padding:10px 12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;font-size:10px">
            <strong>Catatan:</strong> {{ $opname->notes }}
        </div>
    @endif

@endsection
