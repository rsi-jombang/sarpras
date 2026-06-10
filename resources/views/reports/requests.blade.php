@extends('layouts.app')

@section('title', 'Laporan Permintaan Barang')

@section('content')
    <div class="container-fluid px-4 py-4">

        {{-- Header --}}
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
            <div>
                <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">Laporan Permintaan Barang</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="font-size:12.5px">
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}" class="text-decoration-none"
                                style="color:var(--text-muted)">Laporan</a></li>
                        <li class="breadcrumb-item active" style="color:var(--text-secondary)">Permintaan Barang</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.requests.pdf', request()->query()) }}"
                    class="btn btn-danger d-flex align-items-center gap-1"
                    style="border-radius:var(--radius-md);font-size:13px" target="_blank">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
                <a href="{{ route('reports.requests.excel', request()->query()) }}"
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
                <form method="GET" action="{{ route('reports.requests') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:13px;font-weight:500">Unit</label>
                        <select name="unit_id" class="form-select form-select-sm" style="font-size:13px">
                            <option value="">Semua Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:13px;font-weight:500">Status</label>
                        <select name="status" class="form-select form-select-sm" style="font-size:13px">
                            <option value="">Semua Status</option>
                            @foreach (['pending' => 'Menunggu', 'approved' => 'Disetujui', 'partial' => 'Sebagian', 'rejected' => 'Ditolak', 'ready' => 'Siap Ambil', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $val => $lbl)
                                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                                    {{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:13px;font-weight:500">Prioritas</label>
                        <select name="priority" class="form-select form-select-sm" style="font-size:13px">
                            <option value="">Semua</option>
                            @foreach (['low' => 'Rendah', 'normal' => 'Normal', 'high' => 'Tinggi', 'urgent' => 'Mendesak'] as $val => $lbl)
                                <option value="{{ $val }}" {{ request('priority') === $val ? 'selected' : '' }}>
                                    {{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:13px;font-weight:500">Dari Tanggal</label>
                        <input type="date" name="date_from" class="form-control form-control-sm"
                            value="{{ request('date_from') }}" style="font-size:13px">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:13px;font-weight:500">Sampai Tanggal</label>
                        <input type="date" name="date_to" class="form-control form-control-sm"
                            value="{{ request('date_to') }}" style="font-size:13px">
                    </div>
                    <div class="col-md-1 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100"
                            style="border-radius:var(--radius-md);font-size:13px">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('reports.requests') }}" class="btn btn-outline-secondary btn-sm"
                            style="border-radius:var(--radius-md)">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary --}}
        <div class="row g-3 mb-4 animate-in">
            @foreach ([[$summary['total'], 'Total', 'var(--accent-primary)', 'rgba(79,70,229,0.1)'], [$summary['completed'], 'Selesai', '#22c55e', 'rgba(34,197,94,0.1)'], [$summary['rejected'], 'Ditolak', '#ef4444', 'rgba(239,68,68,0.1)'], [$summary['pending'], 'Masih Pending', '#eab308', 'rgba(234,179,8,0.1)']] as [$val, $lbl, $color, $bg])
                <div class="col-6 col-md-3">
                    <div class="card border-0 px-3 py-3"
                        style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);border:1px solid var(--border-subtle)!important">
                        <h3 class="fw-bold mb-0" style="color:{{ $color }}">{{ $val }}</h3>
                        <p class="text-muted mb-0" style="font-size:12.5px">{{ $lbl }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Tabel Preview --}}
        <div class="card border-0 animate-in"
            style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
            <div
                class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Preview Data</h5>
                    <p class="text-muted mb-0" style="font-size:12.5px">Menampilkan {{ $requests->count() }} dari
                        {{ $requests->total() }} permintaan.</p>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" style="font-size:13px">
                        <thead>
                            <tr>
                                <th>No. Permintaan</th>
                                <th>Unit</th>
                                <th>Pengaju</th>
                                <th class="text-center">Prioritas</th>
                                <th class="text-center">Status</th>
                                <th>Tgl. Ajuan</th>
                                <th class="text-center">Item</th>
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
                                    'ready' => ['primary', 'Siap Ambil'],
                                    'completed' => ['success', 'Selesai'],
                                    'cancelled' => ['secondary', 'Dibatalkan'],
                                ];
                                $priorityMap = [
                                    'low' => ['secondary', 'Rendah'],
                                    'normal' => ['info', 'Normal'],
                                    'high' => ['warning', 'Tinggi'],
                                    'urgent' => ['danger', 'Mendesak'],
                                ];
                            @endphp
                            @forelse($requests as $req)
                                @php
                                    [$sColor, $sLabel] = $statusMap[$req->status->value] ?? [
                                        'secondary',
                                        $req->status->value,
                                    ];
                                    [$pColor, $pLabel] = $priorityMap[$req->priority] ?? ['secondary', $req->priority];
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('requests.show', $req) }}" class="fw-bold text-decoration-none"
                                            style="color:var(--accent-primary)">
                                            {{ $req->request_number }}
                                        </a>
                                    </td>
                                    <td>{{ $req->unit->name ?? '—' }}</td>
                                    <td>{{ $req->requester->name ?? '—' }}</td>
                                    <td class="text-center">
                                        <span
                                            class="badge rounded-pill bg-{{ $pColor }}-subtle text-{{ $pColor }}"
                                            style="font-size:10px">{{ $pLabel }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge rounded-pill bg-{{ $sColor }}-subtle text-{{ $sColor }}"
                                            style="font-size:10px">{{ $sLabel }}</span>
                                    </td>
                                    <td>{{ $req->request_date->format('d M Y') }}</td>
                                    <td class="text-center">{{ $req->items->count() }}</td>
                                    <td class="text-muted">{{ $req->received_by_name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox d-block mb-2 fs-2 opacity-50"></i>
                                        Tidak ada data yang cocok dengan filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $requests->withQueryString()->links() }}
            </div>
        </div>

    </div>
@endsection
