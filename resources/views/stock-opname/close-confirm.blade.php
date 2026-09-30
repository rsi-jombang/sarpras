@extends('layouts.app')

@section('title', 'Konfirmasi Tutup Opname - ' . $stockOpname->opname_number)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">Konfirmasi Tutup Opname</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:12.5px">
                    <li class="breadcrumb-item"><a href="{{ route('stock-opname.index') }}" class="text-decoration-none" style="color:var(--text-muted)">Stok Opname</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stock-opname.show', $stockOpname) }}" class="text-decoration-none" style="color:var(--text-muted)">{{ $stockOpname->opname_number }}</a></li>
                    <li class="breadcrumb-item active" style="color:var(--text-secondary)">Konfirmasi Tutup</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('stock-opname.show', $stockOpname) }}" class="btn btn-outline-secondary d-flex align-items-center gap-1"
           style="border-radius:var(--radius-md);font-size:13px">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Error --}}
    @if(session('error'))
        <div class="alert border-0 d-flex align-items-center gap-2 mb-4"
             style="background:rgba(239,68,68,0.1);border-left:4px solid #ef4444!important;border-radius:var(--radius-md);font-size:13px">
            <i class="bi bi-exclamation-circle text-danger"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Peringatan Stok Berubah Sejak Snapshot --}}
    @if($changedCount > 0)
        <div class="alert border-0 d-flex align-items-start gap-3 mb-4 animate-in"
             style="background:rgba(14,165,233,0.08);border-left:4px solid #0ea5e9!important;border-radius:var(--radius-md)">
            <i class="bi bi-arrow-repeat text-info mt-1" style="font-size:1.1rem;flex-shrink:0"></i>
            <div style="font-size:13px">
                <strong style="color:var(--text-primary)">Ada {{ $changedCount }} item yang stoknya berubah sejak snapshot awal</strong><br>
                <span class="text-muted">Terjadi mutasi operasional (PO diterima / permintaan dipenuhi) selama opname berjalan.
                Penyesuaian akan dihitung dari kolom <strong>Stok Terkini</strong>, bukan angka snapshot.</span>
            </div>
        </div>
    @endif

    {{-- Peringatan Item Belum Dihitung --}}
    @if($uncountedItems > 0)
        <div class="alert border-0 d-flex align-items-start gap-3 mb-4 animate-in"
             style="background:rgba(239,68,68,0.08);border-left:4px solid #ef4444!important;border-radius:var(--radius-md)">
            <i class="bi bi-x-circle text-danger mt-1" style="font-size:1.1rem;flex-shrink:0"></i>
            <div style="font-size:13px">
                <strong class="text-danger">Tidak bisa ditutup!</strong><br>
                <span class="text-muted">Masih ada <strong>{{ $uncountedItems }} item</strong> yang belum dihitung secara fisik.
                Kembali ke halaman detail dan selesaikan penghitungan terlebih dahulu.</span>
            </div>
        </div>
    @else
        <div class="alert border-0 d-flex align-items-start gap-3 mb-4 animate-in"
             style="background:rgba(234,179,8,0.08);border-left:4px solid #eab308!important;border-radius:var(--radius-md)">
            <i class="bi bi-exclamation-triangle text-warning mt-1" style="font-size:1.1rem;flex-shrink:0"></i>
            <div style="font-size:13px">
                <strong style="color:var(--text-primary)">Tindakan ini tidak dapat dibatalkan!</strong><br>
                <span class="text-muted">Setelah opname ditutup, sistem akan menyesuaikan stok berdasarkan selisih yang ditemukan.
                Semua pergerakan stok (adjustment) akan dicatat secara otomatis.</span>
            </div>
        </div>
    @endif

    <div class="row g-4">

        {{-- Ringkasan Opname --}}
        <div class="col-lg-4">
            <div class="card border-0 mb-4 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3" style="color:var(--text-secondary);font-size:12px;text-transform:uppercase;letter-spacing:.5px">Ringkasan Opname</h6>

                    @foreach([
                        ['bi-hash',           'No. Opname',       $stockOpname->opname_number],
                        ['bi-calendar3',      'Tanggal Opname',   $stockOpname->opname_date->format('d M Y')],
                        ['bi-person',         'Dibuat Oleh',      $stockOpname->creator->name ?? '—'],
                    ] as [$icon, $label, $value])
                        <div class="d-flex gap-2 mb-2 align-items-start" style="font-size:13px">
                            <i class="bi {{ $icon }} text-muted mt-1" style="width:14px;flex-shrink:0"></i>
                            <span class="text-muted" style="min-width:110px;flex-shrink:0">{{ $label }}</span>
                            <span class="fw-medium" style="color:var(--text-primary)">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Statistik Selisih --}}
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3" style="color:var(--text-secondary);font-size:12px;text-transform:uppercase;letter-spacing:.5px">Statistik Selisih</h6>

                    <div class="row g-3">
                        @foreach([
                            [$totalItems,    'Total Item',      'var(--text-primary)', 'var(--bg-muted)'],
                            [$countedItems,  'Sudah Dihitung',  '#22c55e',             'rgba(34,197,94,0.08)'],
                            [$uncountedItems,'Belum Dihitung',  '#ef4444',             'rgba(239,68,68,0.08)'],
                            [$diffItems,     'Ada Selisih',     '#eab308',             'rgba(234,179,8,0.08)'],
                            [$surplusItems,  'Surplus (+)',     '#0ea5e9',             'rgba(14,165,233,0.08)'],
                            [$deficitItems,  'Defisit (-)',     '#f97316',             'rgba(249,115,22,0.08)'],
                        ] as [$val, $lbl, $color, $bg])
                            <div class="col-6">
                                <div class="p-3 rounded-3 text-center" style="background:{{ $bg }}">
                                    <div class="fw-bold" style="font-size:1.4rem;color:{{ $color }}">{{ $val }}</div>
                                    <div class="text-muted" style="font-size:11.5px">{{ $lbl }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Item dengan Selisih --}}
        <div class="col-lg-8">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Item yang Memiliki Selisih</h5>
                    <p class="text-muted mb-0" style="font-size:12.5px">
                        @if($itemsWithDiff->isEmpty())
                            <span class="text-success"><i class="bi bi-check-circle me-1"></i>Tidak ada selisih. Semua stok cocok.</span>
                        @else
                            {{ $itemsWithDiff->count() }} item berikut akan disesuaikan stoknya setelah opname ditutup.
                        @endif
                    </p>
                </div>
                <div class="card-body px-4 pb-4">
                    @if($itemsWithDiff->isEmpty())
                        <div class="text-center py-5">
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                                 style="width:64px;height:64px;border-radius:50%;background:rgba(34,197,94,0.1)">
                                <i class="bi bi-check-lg text-success" style="font-size:1.8rem"></i>
                            </div>
                            <p class="text-muted mb-0" style="font-size:13px">Stok fisik sesuai dengan stok sistem.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle" style="font-size:13px">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th class="text-center" width="80">Satuan</th>
                                        <th class="text-center" width="90">Stok Sistem</th>
                                        <th class="text-center" width="90">Stok Terkini</th>
                                        <th class="text-center" width="90">Fisik</th>
                                        <th class="text-center" width="90">Selisih</th>
                                        <th width="130">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($itemsWithDiff as $oi)
                                        <tr class="{{ $oi->difference < 0 ? 'table-danger' : 'table-info' }}" style="--bs-table-bg:{{ $oi->difference < 0 ? 'rgba(239,68,68,0.05)' : 'rgba(14,165,233,0.05)' }}">
                                            <td>
                                                <div class="fw-semibold" style="color:var(--text-primary)">{{ $oi->item->name }}</div>
                                                <small class="text-muted">{{ $oi->item->code }}</small>
                                            </td>
                                            <td class="text-center text-muted">{{ $oi->item->unit_of_measure }}</td>
                                            <td class="text-center fw-semibold">{{ $oi->system_quantity }}</td>
                                            <td class="text-center fw-semibold">
                                                {{ $oi->current_total }}
                                                @if($oi->current_total !== (int) $oi->system_quantity)
                                                    <i class="bi bi-exclamation-circle text-info ms-1" title="Berubah sejak snapshot ({{ $oi->system_quantity }} → {{ $oi->current_total }})"></i>
                                                @endif
                                            </td>
                                            <td class="text-center fw-semibold">{{ $oi->physical_quantity }}</td>
                                            <td class="text-center">
                                                @php $sign = $oi->difference > 0 ? '+' : ''; @endphp
                                                <span class="fw-bold {{ $oi->difference > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $sign }}{{ $oi->difference }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($oi->difference > 0)
                                                    <span class="text-success" style="font-size:12px">
                                                        <i class="bi bi-arrow-up-circle me-1"></i>Stok +{{ $oi->difference }}
                                                    </span>
                                                @else
                                                    <span class="text-danger" style="font-size:12px">
                                                        <i class="bi bi-arrow-down-circle me-1"></i>Stok {{ $oi->difference }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- Tombol Aksi --}}
                    @if($uncountedItems === 0)
                        @if($changedCount > 0)
                            <div class="form-check mt-4 p-3 rounded-3" style="background:rgba(14,165,233,0.06);border:1px solid rgba(14,165,233,0.25);font-size:13px">
                                <input class="form-check-input" type="checkbox" id="ackChanged">
                                <label class="form-check-label" for="ackChanged" style="color:var(--text-primary)">
                                    Saya memahami bahwa penyesuaian dihitung dari <strong>Stok Terkini</strong>
                                    ({{ $changedCount }} item berubah sejak snapshot awal).
                                </label>
                            </div>
                        @endif
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3" style="border-top:1px solid var(--border-subtle)">
                            <a href="{{ route('stock-opname.show', $stockOpname) }}"
                               class="btn btn-outline-secondary" style="border-radius:var(--radius-md);font-size:13px">
                                Periksa Lagi
                            </a>
                            <form action="{{ route('stock-opname.close', $stockOpname) }}" method="POST" id="closeForm">
                                @csrf
                                <button type="submit" class="btn btn-success d-flex align-items-center gap-2"
                                        id="btnConfirmClose"
                                        @if($changedCount > 0) disabled @endif
                                        style="border-radius:var(--radius-md);font-size:13px">
                                    <span class="spinner-border spinner-border-sm d-none" id="btnCloseSpinner"></span>
                                    <i class="bi bi-lock"></i>
                                    Ya, Tutup Opname & Sesuaikan Stok
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="d-flex justify-content-end mt-4 pt-3" style="border-top:1px solid var(--border-subtle)">
                            <a href="{{ route('stock-opname.show', $stockOpname) }}"
                               class="btn btn-primary d-flex align-items-center gap-2"
                               style="border-radius:var(--radius-md);background:var(--accent-primary);border-color:var(--accent-primary);font-size:13px">
                                <i class="bi bi-arrow-left"></i> Selesaikan Penghitungan Dulu
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#ackChanged').on('change', function () {
        $('#btnConfirmClose').prop('disabled', !$(this).is(':checked'));
    });
    $('#closeForm').on('submit', function () {
        $('#btnCloseSpinner').removeClass('d-none');
        $('#btnConfirmClose').prop('disabled', true);
    });
});
</script>
@endpush
