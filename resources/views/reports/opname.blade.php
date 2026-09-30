@extends('layouts.app')

@section('title', 'Laporan Stok Opname')

@section('content')
    <div class="container-fluid px-4 py-4">

        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
            <div>
                <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">Laporan Stok Opname</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="font-size:12.5px">
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}" class="text-decoration-none"
                                style="color:var(--text-muted)">Laporan</a></li>
                        <li class="breadcrumb-item active" style="color:var(--text-secondary)">Stok Opname</li>
                    </ol>
                </nav>
            </div>
            @if ($selectedOpname)
                <div class="d-flex gap-2">
                    <a href="{{ route('reports.opname.pdf', ['opname_id' => $selectedOpname->id]) }}"
                        class="btn btn-danger d-flex align-items-center gap-1"
                        style="border-radius:var(--radius-md);font-size:13px" target="_blank">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </a>
                    <a href="{{ route('reports.opname.excel', ['opname_id' => $selectedOpname->id]) }}"
                        class="btn btn-success d-flex align-items-center gap-1"
                        style="border-radius:var(--radius-md);font-size:13px">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </a>
                </div>
            @endif
        </div>

        <div class="row g-4">

            {{-- Kolom Kiri: Pilih Opname --}}
            <div class="col-lg-4">
                <div class="card border-0 animate-in"
                    style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Pilih Opname</h5>
                        <p class="text-muted mb-0" style="font-size:12.5px">Pilih opname yang sudah ditutup untuk dilihat &
                            diekspor.</p>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @forelse($opnames as $opname)
                            <a href="{{ route('reports.opname', ['opname_id' => $opname->id]) }}"
                                class="d-block p-3 rounded-3 mb-2 text-decoration-none {{ $selectedOpname?->id === $opname->id ? 'border border-primary' : '' }}"
                                style="background:{{ $selectedOpname?->id === $opname->id ? 'rgba(79,70,229,0.06)' : 'var(--bg-muted)' }};
                                  border:1px solid {{ $selectedOpname?->id === $opname->id ? 'var(--accent-primary)' : 'var(--border-subtle)' }}!important;
                                  transition:all .15s">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold"
                                            style="font-size:13px;color:{{ $selectedOpname?->id === $opname->id ? 'var(--accent-primary)' : 'var(--text-primary)' }}">
                                            {{ $opname->opname_number }}
                                        </div>
                                        <div class="text-muted" style="font-size:12px">
                                            {{ $opname->opname_date->format('d M Y') }}
                                            @if ($opname->closed_date)
                                                &nbsp;·&nbsp; Tutup: {{ $opname->closed_date->format('d M Y') }}
                                            @endif
                                        </div>
                                        <div class="text-muted" style="font-size:11.5px">
                                            {{ $opname->creator->name ?? '—' }}</div>
                                    </div>
                                    @if ($selectedOpname?->id === $opname->id)
                                        <i class="bi bi-check-circle-fill"
                                            style="color:var(--accent-primary);font-size:1rem;flex-shrink:0"></i>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-5 text-muted" style="font-size:13px">
                                <i class="bi bi-clipboard-x d-block mb-2 fs-2 opacity-50"></i>
                                Belum ada opname yang ditutup.
                            </div>
                        @endforelse

                        {{ $opnames->links() }}
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Preview Data --}}
            <div class="col-lg-8">
                @if ($selectedOpname)

                    {{-- Info Opname Terpilih --}}
                    <div class="card border-0 mb-4 animate-in"
                        style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                        <div class="card-body p-4">
                            <div class="row g-3" style="font-size:13px">
                                <div class="col-md-3">
                                    <span class="text-muted d-block"
                                        style="font-size:11px;text-transform:uppercase;letter-spacing:.5px">No.
                                        Opname</span>
                                    <span class="fw-bold"
                                        style="color:var(--accent-primary)">{{ $selectedOpname->opname_number }}</span>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted d-block"
                                        style="font-size:11px;text-transform:uppercase;letter-spacing:.5px">Tgl.
                                        Opname</span>
                                    <span class="fw-semibold">{{ $selectedOpname->opname_date->format('d M Y') }}</span>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted d-block"
                                        style="font-size:11px;text-transform:uppercase;letter-spacing:.5px">Dibuat
                                        Oleh</span>
                                    <span class="fw-semibold">{{ $selectedOpname->creator->name ?? '—' }}</span>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted d-block"
                                        style="font-size:11px;text-transform:uppercase;letter-spacing:.5px">Ditutup
                                        Oleh</span>
                                    <span class="fw-semibold">{{ $selectedOpname->closer?->name ?? '—' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Summary Selisih --}}
                    @php
                        $totalItems = $opnameItems->count();
                        $diffItems = $opnameItems->where('difference', '!=', 0)->whereNotNull('difference')->count();
                        $surplusItems = $opnameItems->where('difference', '>', 0)->count();
                        $deficitItems = $opnameItems->where('difference', '<', 0)->count();
                        $matchItems = $opnameItems->where('difference', 0)->count();
                    @endphp
                    <div class="row g-3 mb-4 animate-in">
                        @foreach ([[$totalItems, 'Total Item', 'var(--accent-primary)', 'rgba(79,70,229,0.1)'], [$matchItems, 'Sesuai', '#22c55e', 'rgba(34,197,94,0.1)'], [$surplusItems, 'Surplus (+)', '#0ea5e9', 'rgba(14,165,233,0.1)'], [$deficitItems, 'Defisit (-)', '#ef4444', 'rgba(239,68,68,0.1)']] as [$val, $lbl, $color, $bg])
                            <div class="col-6 col-md-3">
                                <div class="card border-0 px-3 py-3"
                                    style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);border:1px solid var(--border-subtle)!important">
                                    <h3 class="fw-bold mb-0" style="color:{{ $color }}">{{ $val }}</h3>
                                    <p class="text-muted mb-0" style="font-size:12.5px">{{ $lbl }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Tabel Item --}}
                    <div class="card border-0 animate-in"
                        style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Hasil per Item</h5>
                            <p class="text-muted mb-0" style="font-size:12.5px">{{ $totalItems }} item ·
                                {{ $diffItems }} item memiliki selisih.</p>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table class="table align-middle" style="font-size:13px">
                                    <thead>
                                        <tr>
                                            <th>Barang</th>
                                            <th class="text-center">Satuan</th>
                                            <th class="text-center">Sistem</th>
                                            <th class="text-center">Fisik</th>
                                            <th class="text-center">Selisih</th>
                                            <th>Keterangan</th>
                                            <th>Dihitung Oleh</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($opnameItems as $oi)
                                            @php
                                                $diff = $oi->difference;
                                                $rowCls =
                                                    $diff === null || $diff === 0
                                                        ? ''
                                                        : ($diff > 0
                                                            ? 'table-info'
                                                            : 'table-danger');
                                            @endphp
                                            <tr class="{{ $rowCls }}"
                                                style="--bs-table-bg:{{ $diff > 0 ? 'rgba(14,165,233,0.05)' : ($diff < 0 ? 'rgba(239,68,68,0.05)' : '') }}">
                                                <td>
                                                    <div class="fw-semibold" style="color:var(--text-primary)">
                                                        {{ $oi->item->name }}</div>
                                                    <small class="text-muted">{{ $oi->item->code }}</small>
                                                </td>
                                                <td class="text-center text-muted">{{ $oi->item->unit_of_measure }}</td>
                                                <td class="text-center fw-semibold">{{ $oi->system_quantity }}</td>
                                                <td class="text-center fw-semibold">{{ $oi->physical_quantity ?? '—' }}
                                                </td>
                                                <td class="text-center">
                                                    @if ($diff === null)
                                                        <span class="text-muted">—</span>
                                                    @elseif($diff === 0)
                                                        <span class="text-success fw-bold">0</span>
                                                    @else
                                                        <span
                                                            class="fw-bold {{ $diff > 0 ? 'text-info' : 'text-danger' }}">
                                                            {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td style="font-size:12px">
                                                    @if ($diff === null)
                                                        <span class="text-muted">—</span>
                                                    @elseif($diff === 0)
                                                        <span class="text-success">✓ Sesuai</span>
                                                    @elseif($diff > 0)
                                                        <span class="text-info">Surplus +{{ $diff }}</span>
                                                    @else
                                                        <span class="text-danger">Defisit {{ $diff }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-muted" style="font-size:12px">{{ $oi->counter?->name ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Belum pilih opname --}}
                    <div class="card border-0 h-100 animate-in d-flex align-items-center justify-content-center"
                        style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);min-height:400px">
                        <div class="text-center p-5">
                            <div class="mx-auto mb-4 d-flex align-items-center justify-content-center"
                                style="width:72px;height:72px;border-radius:var(--radius-lg);background:rgba(79,70,229,0.1)">
                                <i class="bi bi-clipboard-data" style="font-size:2rem;color:var(--accent-primary)"></i>
                            </div>
                            <h5 class="fw-bold mb-2" style="color:var(--text-primary)">Pilih Opname</h5>
                            <p class="text-muted mb-0" style="font-size:13px">
                                Pilih salah satu opname dari daftar di sebelah kiri<br>untuk melihat detail dan mengekspor
                                laporan.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
