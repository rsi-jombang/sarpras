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
            <div class="label">Periode</div>
            <div class="value">
                {{ \Carbon\Carbon::parse($filters['date_from'])->format('d M Y') }} s/d
                {{ \Carbon\Carbon::parse($filters['date_to'])->format('d M Y') }}
            </div>
        </div>
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
                <th>Satuan</th>
                <th class="text-right">Stok Awal</th>
                <th class="text-right">Masuk</th>
                <th class="text-right">Keluar</th>
                <th class="text-right">Tersedia</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $i => $item)
                @php
                    $sum = $summary[$item->id] ?? ['awal' => 0, 'masuk' => 0, 'keluar' => 0, 'tersedia' => (int) ($item->total_stock ?? 0)];
                    $tersedia = (int) $sum['tersedia'];
                    $isLow = $item->stocks
                        ->filter(fn($s) => $s->minimum_stock > 0 && $s->quantity <= $s->minimum_stock)
                        ->isNotEmpty();
                    $status = $tersedia === 0 ? 'Habis' : ($isLow ? 'Rendah' : 'Normal');
                    $sBadge = $tersedia === 0 ? 'danger' : ($isLow ? 'warning' : 'success');
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-muted">{{ $item->code }}</td>
                    <td class="fw-bold">{{ $item->name }}</td>
                    <td>{{ $item->category->name ?? '—' }}</td>
                    <td>{{ $item->unit_of_measure }}</td>
                    <td class="text-right">{{ (int) $sum['awal'] }}</td>
                    <td class="text-right">{{ (int) $sum['masuk'] }}</td>
                    <td class="text-right">{{ (int) $sum['keluar'] }}</td>
                    <td class="text-right fw-bold {{ $tersedia === 0 ? 'text-danger' : ($isLow ? '' : 'text-success') }}">
                        {{ $tersedia }}</td>
                    <td class="text-center"><span class="badge badge-{{ $sBadge }}">{{ $status }}</span></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right">Total:</td>
                <td class="text-right">{{ $items->sum(fn($i) => (int) ($summary[$i->id]['awal'] ?? 0)) }}</td>
                <td class="text-right">{{ $items->sum(fn($i) => (int) ($summary[$i->id]['masuk'] ?? 0)) }}</td>
                <td class="text-right">{{ $items->sum(fn($i) => (int) ($summary[$i->id]['keluar'] ?? 0)) }}</td>
                <td class="text-right">{{ $items->sum(fn($i) => (int) ($summary[$i->id]['tersedia'] ?? 0)) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
@endsection
