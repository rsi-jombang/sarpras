@extends('reports.pdf._layout')

@section('title', 'Laporan Kondisi Stok Barang')

@section('content')

    <div class="meta-grid">
        @if (!empty($filters['item_type']))
            <div class="meta-item">
                <div class="label">Tipe</div>
                <div class="value">{{ $filters['item_type'] === 'consumable' ? 'Habis Pakai' : 'Inventaris' }}</div>
            </div>
        @endif
        @if (!empty($filters['stock_status']))
            <div class="meta-item">
                <div class="label">Filter Stok</div>
                <div class="value">{{ $filters['stock_status'] === 'low' ? 'Stok Rendah' : 'Stok Habis' }}</div>
            </div>
        @endif
        <div class="meta-item">
            <div class="label">Total Barang</div>
            <div class="value">{{ $items->count() }} item</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Tipe</th>
                <th>Satuan</th>
                <th class="text-right">Total Stok</th>
                <th class="text-center">Batch</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $i => $item)
                @php
                    $total = (int) ($item->total_stock ?? 0);
                    $isLow = $item->stocks
                        ->filter(fn($s) => $s->minimum_stock > 0 && $s->quantity <= $s->minimum_stock)
                        ->isNotEmpty();
                    $status = $total === 0 ? 'Habis' : ($isLow ? 'Rendah' : 'Normal');
                    $sBadge = $total === 0 ? 'danger' : ($isLow ? 'warning' : 'success');
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-muted">{{ $item->code }}</td>
                    <td class="fw-bold">{{ $item->name }}</td>
                    <td>{{ $item->category->name ?? '—' }}</td>
                    <td>{{ $item->item_type->label() }}</td>
                    <td>{{ $item->unit_of_measure }}</td>
                    <td class="text-right fw-bold {{ $total === 0 ? 'text-danger' : ($isLow ? '' : 'text-success') }}">
                        {{ $total }}</td>
                    <td class="text-center">{{ $item->stocks->count() }}</td>
                    <td class="text-center"><span class="badge badge-{{ $sBadge }}">{{ $status }}</span></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right">Total Stok Keseluruhan:</td>
                <td class="text-right">{{ $items->sum('total_stock') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
@endsection
