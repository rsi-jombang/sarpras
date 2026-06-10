@extends('layouts.app')

@section('title', 'Laporan Kondisi Stok Barang')

@section('content')
    <div class="container-fluid px-4 py-4">

        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
            <div>
                <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">Laporan Kondisi Stok Barang</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="font-size:12.5px">
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}" class="text-decoration-none"
                                style="color:var(--text-muted)">Laporan</a></li>
                        <li class="breadcrumb-item active" style="color:var(--text-secondary)">Kondisi Stok</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.stocks.pdf', request()->query()) }}"
                    class="btn btn-danger d-flex align-items-center gap-1"
                    style="border-radius:var(--radius-md);font-size:13px" target="_blank">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
                <a href="{{ route('reports.stocks.excel', request()->query()) }}"
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
                <form method="GET" action="{{ route('reports.stocks') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:13px;font-weight:500">Tipe Barang</label>
                        <select name="item_type" class="form-select form-select-sm" style="font-size:13px">
                            <option value="">Semua Tipe</option>
                            <option value="consumable" {{ request('item_type') === 'consumable' ? 'selected' : '' }}>Habis
                                Pakai</option>
                            <option value="inventory" {{ request('item_type') === 'inventory' ? 'selected' : '' }}>
                                Inventaris</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:13px;font-weight:500">Kategori</label>
                        <select name="category_id" class="form-select form-select-sm" style="font-size:13px">
                            <option value="">Semua Kategori</option>
                            @foreach (\App\Models\Category::active()->orderBy('name')->get() as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:13px;font-weight:500">Status Stok</label>
                        <select name="stock_status" class="form-select form-select-sm" style="font-size:13px">
                            <option value="">Semua Status</option>
                            <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Stok Rendah
                            </option>
                            <option value="empty" {{ request('stock_status') === 'empty' ? 'selected' : '' }}>Stok Habis
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm"
                            style="border-radius:var(--radius-md);font-size:13px">
                            <i class="bi bi-search me-1"></i>Terapkan
                        </button>
                        <a href="{{ route('reports.stocks') }}" class="btn btn-outline-secondary btn-sm"
                            style="border-radius:var(--radius-md);font-size:13px">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel Preview --}}
        <div class="card border-0 animate-in"
            style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Preview Data</h5>
                <p class="text-muted mb-0" style="font-size:12.5px">
                    Menampilkan {{ $items->count() }} dari {{ $items->total() }} barang.
                    Total stok: <strong>{{ number_format($items->sum('total_stock')) }}</strong> unit.
                </p>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" style="font-size:13px">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th class="text-center">Tipe</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Total Stok</th>
                                <th class="text-center">Batch</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                @php
                                    $total = (int) ($item->total_stock ?? 0);
                                    $isLow = $item->stocks
                                        ->filter(fn($s) => $s->minimum_stock > 0 && $s->quantity <= $s->minimum_stock)
                                        ->isNotEmpty();
                                    $status =
                                        $total === 0
                                            ? ['danger', 'Habis']
                                            : ($isLow
                                                ? ['warning', 'Rendah']
                                                : ['success', 'Normal']);
                                @endphp
                                <tr>
                                    <td class="text-muted" style="font-size:12px">{{ $item->code }}</td>
                                    <td>
                                        <a href="{{ route('items.show', $item) }}" class="fw-semibold text-decoration-none"
                                            style="color:var(--text-primary)">
                                            {{ $item->name }}
                                        </a>
                                    </td>
                                    <td>{{ $item->category->name ?? '—' }}</td>
                                    <td class="text-center">
                                        @if ($item->item_type->value === 'consumable')
                                            <span class="badge rounded-pill bg-info-subtle text-info"
                                                style="font-size:10px">Habis Pakai</span>
                                        @else
                                            <span class="badge rounded-pill bg-purple-subtle text-purple"
                                                style="font-size:10px">Inventaris</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-muted">{{ $item->unit_of_measure }}</td>
                                    <td class="text-center">
                                        <span class="fw-bold text-{{ $status[0] }}">{{ $total }}</span>
                                    </td>
                                    <td class="text-center text-muted">{{ $item->stocks->count() }}</td>
                                    <td class="text-center">
                                        <span
                                            class="badge rounded-pill bg-{{ $status[0] }}-subtle text-{{ $status[0] }}"
                                            style="font-size:10px">
                                            {{ $status[1] }}
                                        </span>
                                    </td>
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
                    </table>
                </div>
                {{ $items->withQueryString()->links() }}
            </div>
        </div>

    </div>
@endsection
