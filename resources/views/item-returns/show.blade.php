@extends('layouts.app')

@section('title', 'Detail Pengembalian - ' . $itemReturn->return_number)

@section('content')
<div class="container-fluid px-4 py-4">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">{{ $itemReturn->return_number }}</h1>
            <p class="text-muted mb-0" style="font-size:13px">Status: <strong>{{ ucfirst($itemReturn->status) }}</strong>
                @if($itemReturn->reason) · Alasan: {{ $itemReturn->reason }}@endif
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @can('update', $itemReturn)
                <a href="{{ route('item-returns.edit', $itemReturn) }}" class="btn btn-warning btn-sm">Edit Draft</a>
            @endcan
            @can('submit', $itemReturn)
                <form action="{{ route('item-returns.submit', $itemReturn) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-send me-1"></i>Kirim Pengembalian</button>
                </form>
            @endcan
            @can('cancel', $itemReturn)
                <form action="{{ route('item-returns.cancel', $itemReturn) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Batalkan pengembalian ini?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">Batalkan</button>
                </form>
            @endcan
            @can('receive', $itemReturn)
                <form action="{{ route('item-returns.receive', $itemReturn) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-box-arrow-in-down me-1"></i>Terima Pengembalian</button>
                </form>
            @endcan
        </div>
    </div>

    <div class="card border-0 p-4" style="background:var(--bg-surface);border-radius:var(--radius-lg)">
        <dl class="row mb-0" style="font-size:13px">
            <dt class="col-sm-3 text-muted">Unit</dt>
            <dd class="col-sm-9">{{ $itemReturn->unit->name ?? '—' }}</dd>
            <dt class="col-sm-3 text-muted">Dibuat Oleh</dt>
            <dd class="col-sm-9">{{ $itemReturn->creator->name ?? '—' }}</dd>
            <dt class="col-sm-3 text-muted">Tanggal Dibuat</dt>
            <dd class="col-sm-9">{{ optional($itemReturn->created_at)->format('d M Y H:i') }}</dd>
            @if($itemReturn->received_by)
            <dt class="col-sm-3 text-muted">Diterima Oleh</dt>
            <dd class="col-sm-9">{{ $itemReturn->receiver->name ?? '—' }} · {{ optional($itemReturn->received_at)->format('d M Y H:i') }}</dd>
            @endif
            @if($itemReturn->notes)
            <dt class="col-sm-3 text-muted">Catatan</dt>
            <dd class="col-sm-9">{{ $itemReturn->notes }}</dd>
            @endif
        </dl>
        <hr>
        <h6 class="fw-bold mb-2" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px">Barang yang Dikembalikan</h6>
        <table class="table table-sm">
            <thead>
                <tr><th>Barang</th><th class="text-center">Qty Dikembalikan</th><th class="text-center">Kondisi</th><th>Catatan</th></tr>
            </thead>
            <tbody>
                @foreach($itemReturn->items as $it)
                <tr>
                    <td>{{ $it->item->name }} <br><small class="text-muted">{{ $it->item->code }}</small>
                        @if($it->requestItem) <br><small class="text-muted">Dari request item #{{ $it->request_item_id }}</small> @endif
                    </td>
                    <td class="text-center">{{ $it->quantity_returned }}</td>
                    <td class="text-center"><span class="badge bg-{{ $it->condition === 'usable' ? 'success' : 'danger' }}-subtle text-{{ $it->condition === 'usable' ? 'success' : 'danger' }}">{{ $it->condition }}</span></td>
                    <td>{{ $it->notes ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection