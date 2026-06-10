<table>
    <thead>
        <tr>
            <th colspan="8" style="font-size:16px;font-weight:bold;text-align:center">
                LAPORAN STOK OPNAME
            </th>
        </tr>
        <tr>
            <td colspan="8" style="text-align:center;font-size:11px">
                {{ $opname->opname_number }} | {{ $opname->opname_date->format('d M Y') }}
                @if($opname->isClosed())
                    | Ditutup: {{ $opname->closed_date?->format('d M Y') }} oleh {{ $opname->closer?->name ?? '—' }}
                @endif
            </td>
        </tr>
        <tr><td colspan="8"></td></tr>
        <tr>
            <th style="font-weight:bold;background:#f0f0f0;text-align:center;border:1px solid #ccc">No</th>
            <th style="font-weight:bold;background:#f0f0f0;text-align:center;border:1px solid #ccc">Kode Barang</th>
            <th style="font-weight:bold;background:#f0f0f0;text-align:center;border:1px solid #ccc">Nama Barang</th>
            <th style="font-weight:bold;background:#f0f0f0;text-align:center;border:1px solid #ccc">Kategori</th>
            <th style="font-weight:bold;background:#f0f0f0;text-align:center;border:1px solid #ccc">Batch</th>
            <th style="font-weight:bold;background:#f0f0f0;text-align:center;border:1px solid #ccc">Stok Sistem</th>
            <th style="font-weight:bold;background:#f0f0f0;text-align:center;border:1px solid #ccc">Stok Fisik</th>
            <th style="font-weight:bold;background:#f0f0f0;text-align:center;border:1px solid #ccc">Selisih</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $oi)
            @php
                $diff = $oi->difference;
                $diffLabel = $diff === null ? '—' : ($diff > 0 ? '+' . $diff : $diff);
            @endphp
            <tr>
                <td style="text-align:center;border:1px solid #ccc">{{ $loop->iteration }}</td>
                <td style="text-align:center;border:1px solid #ccc">{{ $oi->item->code ?? '—' }}</td>
                <td style="border:1px solid #ccc">{{ $oi->item->name ?? '—' }}</td>
                <td style="border:1px solid #ccc">{{ $oi->item->category->name ?? '—' }}</td>
                <td style="text-align:center;border:1px solid #ccc">{{ $oi->itemStock?->batch_number ?? '—' }}</td>
                <td style="text-align:center;border:1px solid #ccc">{{ $oi->system_quantity }}</td>
                <td style="text-align:center;border:1px solid #ccc">{{ $oi->physical_quantity ?? '—' }}</td>
                <td style="text-align:center;border:1px solid #ccc;font-weight:bold">{{ $diffLabel }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr><td colspan="8"></td></tr>
        <tr>
            <td colspan="8" style="font-size:10px;color:#666">
                Dicetak: {{ now()->format('d M Y H:i') }} |
                Dibuat oleh: {{ $opname->creator->name ?? '—' }} |
                Status: {{ $opname->isClosed() ? 'Ditutup' : ($opname->status === 'in_progress' ? 'Sedang Berjalan' : 'Terbuka') }}
            </td>
        </tr>
    </tfoot>
</table>
