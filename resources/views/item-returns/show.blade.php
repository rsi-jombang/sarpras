@extends('layouts.app')

@section('title', 'Detail Pengembalian - ' . $itemReturn->return_number)

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">{{ $itemReturn->return_number }}</h1>
            <p class="text-muted mb-0" style="font-size:13px">Status: <strong>{{ ucfirst($itemReturn->status) }}</strong></p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @can('update', $itemReturn)
                <a href="{{ route('item-returns.edit', $itemReturn) }}" class="btn btn-warning btn-sm">Edit Draft</a>
            @endcan
            @can('submit', $itemReturn)
                <form action="{{ route('item-returns.submit', $itemReturn) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">Kirim Pengembalian</button>
                </form>
            @endcan
            @can('cancel', $itemReturn)
                <form action="{{ route('item-returns.cancel', $itemReturn) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">Batalkan</button>
                </form>
            @endcan
            @can('receive', $itemReturn)
                <form action="{{ route('item-returns.receive', $itemReturn) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">Terima Pengembalian</button>
                </form>
            @endcan
        </div>
    </div>

    <div class="card border-0 p-4" style="background:var(--bg-surface);border-radius:var(--radius-lg)">
        <table class="table table-sm">
            <thead>
                <tr><th>Barang</th><th class="text-center">Qty Dikembalikan</th><th class="text-center">Kondisi</th><th>Catatan</th></tr>
            </thead>
            <tbody>
                @foreach($itemReturn->items as $it)
                <tr>
                    <td>{{ $it->item->name }}<br><small class="text-muted">{{ $it->item->code }}</small>
                        @if($it->requestItem) <br><small class="text-muted">Request item #{{ $it->request_item_id }}</small> @endif
                    </td>
                    <td class="text-center">{{ $it->quantity_returned }}</td>
                    <td class="text-center">{{ $it->condition }}</td>
                    <td>{{ $it->notes ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($itemReturn->notes)
            <p class="text-muted mt-2 small">{{ $itemReturn->notes }}</p>
        @endif
    </div>
</div>
@endsection