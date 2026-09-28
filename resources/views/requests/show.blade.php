@extends('layouts.app')

@section('title', 'Detail Permintaan - ' . $request->request_number)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">{{ $request->request_number }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:12.5px">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none" style="color:var(--text-muted)">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('requests.index') }}" class="text-decoration-none" style="color:var(--text-muted)">Permintaan</a></li>
                    <li class="breadcrumb-item active" style="color:var(--text-secondary)">{{ $request->request_number }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            {{-- Download PDF --}}
            <a href="{{ route('requests.pdf', $request) }}"
               class="btn btn-outline-primary d-flex align-items-center gap-1"
               style="border-radius:var(--radius-md);font-size:13px">
                <i class="bi bi-file-pdf"></i> Download PDF
            </a>

            {{-- Unit: Edit Draft --}}
            @can('update', $request)
                <a href="{{ route('requests.edit', $request) }}"
                   class="btn btn-warning d-flex align-items-center gap-1"
                   style="border-radius:var(--radius-md);font-size:13px">
                    <i class="bi bi-pencil"></i> Edit Draft
                </a>
            @endcan

            {{-- Unit: Kirim Draft --}}
            @can('submit', $request)
                <button type="button" id="btnSubmit"
                        class="btn btn-success d-flex align-items-center gap-1"
                        style="border-radius:var(--radius-md);font-size:13px">
                    <i class="bi bi-send"></i> Kirim Permintaan
                </button>
            @endcan

            {{-- Sarpras: Review --}}
            @can('review', $request)
                <a href="{{ route('requests.review.form', $request) }}"
                   class="btn btn-warning d-flex align-items-center gap-1"
                   style="border-radius:var(--radius-md);font-size:13px">
                    <i class="bi bi-pencil-square"></i> Review Permintaan
                </a>
            @endcan

            {{-- Sarpras: Siapkan Barang --}}
            @can('fulfill', $request)
                <button type="button" id="btnFulfill"
                        class="btn btn-info text-white d-flex align-items-center gap-1"
                        style="border-radius:var(--radius-md);font-size:13px">
                    <i class="bi bi-box-arrow-in-down"></i> Siapkan Barang
                </button>
            @endcan

            {{-- Sarpras: Konfirmasi Pengambilan --}}
            @can('complete', $request)
                <button type="button" id="btnComplete"
                        class="btn btn-success d-flex align-items-center gap-1"
                        style="border-radius:var(--radius-md);font-size:13px">
                    <i class="bi bi-check-lg"></i> Konfirmasi Pengambilan
                </button>
            @endcan

            {{-- Unit: Batalkan --}}
            @can('cancel', $request)
                <button type="button" id="btnCancel"
                        class="btn btn-outline-danger d-flex align-items-center gap-1"
                        style="border-radius:var(--radius-md);font-size:13px">
                    <i class="bi bi-x-lg"></i> Batalkan
                </button>
            @endcan
        </div>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert border-0 d-flex align-items-center gap-2 mb-4"
             style="background:rgba(34,197,94,0.1);border-left:4px solid #22c55e!important;border-radius:var(--radius-md);font-size:13px">
            <i class="bi bi-check-circle text-success"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">

        {{-- Kolom Kiri --}}
        <div class="col-lg-4">

            {{-- Status & Timeline --}}
            <div class="card border-0 mb-4 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-body p-4">
                    @php
                        $statusMap = [
                            'draft'     => ['secondary', 'Draft'],
                            'pending'   => ['warning',   'Menunggu Review'],
                            'approved'  => ['info',      'Disetujui'],
                            'partial'   => ['info',      'Disetujui Sebagian'],
                            'rejected'  => ['danger',    'Ditolak'],
                            'ready'     => ['primary',   'Siap Diambil'],
                            'completed' => ['success',   'Selesai'],
                            'cancelled' => ['secondary', 'Dibatalkan'],
                        ];
                        [$statusColor, $statusLabel] = $statusMap[$request->status->value] ?? ['secondary', '—'];
                    @endphp
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold mb-0" style="color:var(--text-secondary);font-size:12px;text-transform:uppercase;letter-spacing:.5px">Status</h6>
                        <span class="badge rounded-pill bg-{{ $statusColor }}-subtle text-{{ $statusColor }} px-3 py-2" style="font-size:12.5px">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    {{-- Timeline --}}
                    <div class="timeline" style="font-size:12.5px">
                        @php
                            $timeline = [
                                ['draft',     'bi-file-earmark',    'Draft',      $request->created_at],
                                ['pending',   'bi-clipboard-plus',  'Diajukan',   $request->status->value === 'draft' ? null : $request->created_at],
                                ['approved',  'bi-check-circle',    'Direview',   $request->reviewed_at],
                                ['ready',     'bi-box-seam',        'Disiapkan',  $request->served_at],
                                ['completed', 'bi-bag-check',       'Diterima',   $request->received_at],
                            ];
                            $order = ['draft','pending','approved','partial','ready','completed','cancelled'];
                            $currentIdx = array_search($request->status->value, $order);
                        @endphp

                        @foreach($timeline as $i => [$s, $icon, $lbl, $time])
                            @php
                                $stepIdx = array_search($s, $order);
                                $isDone  = $currentIdx >= $stepIdx && !in_array($request->status->value, ['rejected','cancelled']);
                                $isActive = $request->status->value === $s;
                                $rejected = in_array($request->status->value, ['rejected','cancelled']) && $i > 0;
                            @endphp
                            <div class="d-flex gap-3 mb-3 {{ $rejected ? 'opacity-25' : '' }}">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:32px;height:32px;background:{{ $isDone ? 'var(--accent-primary)' : 'var(--bg-muted)' }};
                                                border:2px solid {{ $isDone ? 'var(--accent-primary)' : 'var(--border-subtle)' }}">
                                        <i class="bi {{ $icon }}" style="font-size:13px;color:{{ $isDone ? '#fff' : 'var(--text-muted)' }}"></i>
                                    </div>
                                    @if(!$loop->last)
                                        <div style="width:2px;flex:1;min-height:20px;background:{{ $isDone ? 'var(--accent-primary)' : 'var(--border-subtle)' }};margin:4px 0;opacity:0.4"></div>
                                    @endif
                                </div>
                                <div class="pb-1">
                                    <div class="fw-semibold" style="color:var(--text-primary)">{{ $lbl }}</div>
                                    @if($time)
                                        <div class="text-muted" style="font-size:11.5px">
                                            {{ $time instanceof \Carbon\Carbon ? $time->format('d M Y, H:i') : \Carbon\Carbon::parse($time)->format('d M Y, H:i') }}
                                        </div>
                                    @else
                                        <div class="text-muted" style="font-size:11.5px">—</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if($request->status->value === 'rejected')
                            <div class="mt-2 p-2 rounded-2" style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);font-size:12px">
                                <i class="bi bi-x-circle text-danger me-1"></i>
                                <strong class="text-danger">Alasan penolakan:</strong><br>
                                <span class="text-muted">{{ $request->review_notes ?? '—' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Info Permintaan --}}
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3" style="color:var(--text-secondary);font-size:12px;text-transform:uppercase;letter-spacing:.5px">Informasi Permintaan</h6>
                    @foreach([
                        ['bi-building',          'Unit',            $request->unit->name ?? '—'],
                        ['bi-person',            'Pengaju',         $request->requester->name ?? '—'],
                        ['bi-flag',              'Prioritas',       ucfirst($request->priority)],
                        ['bi-calendar3',         'Tgl. Ajuan',      $request->request_date->format('d M Y')],
                        ['bi-calendar-event',    'Tgl. Butuh',      $request->needed_date?->format('d M Y') ?? '—'],
                        ['bi-person-check',      'Direview Oleh',   $request->reviewer->name ?? '—'],
                        ['bi-person-badge',      'Disiapkan Oleh',  $request->server->name ?? '—'],
                        ['bi-person-lines-fill', 'Diambil Oleh',    $request->received_by_name ?? '—'],
                    ] as [$icon, $label, $value])
                        <div class="d-flex gap-2 mb-2 align-items-start" style="font-size:13px">
                            <i class="bi {{ $icon }} text-muted mt-1" style="width:16px;flex-shrink:0"></i>
                            <span class="text-muted" style="min-width:110px;flex-shrink:0">{{ $label }}</span>
                            <span class="fw-medium" style="color:var(--text-primary)">{{ $value }}</span>
                        </div>
                    @endforeach
                    @if($request->purpose)
                        <div class="mt-3 p-2 rounded-2" style="background:var(--bg-muted);font-size:12.5px">
                            <i class="bi bi-chat-left-text me-1 text-muted"></i>
                            <strong style="color:var(--text-secondary)">Keperluan:</strong>
                            <span class="text-muted"> {{ $request->purpose }}</span>
                        </div>
                    @endif
                    @if($request->notes)
                        <div class="mt-2 p-2 rounded-2" style="background:var(--bg-muted);font-size:12.5px">
                            <i class="bi bi-sticky me-1 text-muted"></i>
                            <span class="text-muted">{{ $request->notes }}</span>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Kolom Kanan: Daftar Barang --}}
        <div class="col-lg-8">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Daftar Barang</h5>
                    <p class="text-muted mb-0" style="font-size:12.5px">{{ $request->items->count() }} jenis barang diminta.</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="table-responsive">
                        <table class="table align-middle" style="font-size:13px">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th class="text-center">Diminta</th>
                                    <th class="text-center">Disetujui</th>
                                    <th class="text-center">Diberikan</th>
                                    <th class="text-center">Status</th>
                                    <th>Batch</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($request->items as $item)
                                    @php
                                        $itemStatusMap = [
                                            'pending'  => ['warning', 'Menunggu'],
                                            'approved' => ['success', 'Disetujui'],
                                            'partial'  => ['info',    'Sebagian'],
                                            'rejected' => ['danger',  'Ditolak'],
                                        ];
                                        [$iColor, $iLabel] = $itemStatusMap[$item->status] ?? ['secondary', '—'];
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold" style="color:var(--text-primary)">{{ $item->item->name }}</div>
                                            <small class="text-muted">{{ $item->item->code }} · {{ $item->item->category->name }}</small>
                                            @if($item->rejection_reason)
                                                <div class="text-danger mt-1" style="font-size:11.5px">
                                                    <i class="bi bi-x-circle me-1"></i>{{ $item->rejection_reason }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center fw-semibold">
                                            {{ $item->quantity_requested }}
                                            <small class="text-muted d-block" style="font-size:11px">{{ $item->item->unit_of_measure }}</small>
                                        </td>
                                        <td class="text-center">
                                            {{ $item->quantity_approved ?? '—' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $item->quantity_given ?? '—' }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-{{ $iColor }}-subtle text-{{ $iColor }} px-2 py-1" style="font-size:11px">
                                                {{ $iLabel }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $item->itemStock?->batch_number ?? '—' }}
                                        </td>
                                        <td>
                                            <span class="text-muted" style="font-size:12px">{{ $item->notes ?? '—' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Pengambilan --}}
<div class="modal fade" id="completeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-0 pb-0" style="background:var(--bg-surface)">
                <h5 class="modal-title fw-bold" style="color:var(--text-primary)">Konfirmasi Pengambilan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="completeForm">
                <div class="modal-body px-4" style="background:var(--bg-surface)">
                    <p class="text-muted mb-3" style="font-size:13px">
                        Isi nama petugas yang mengambil barang sebagai bukti penerimaan.
                    </p>
                    <label class="form-label" style="font-size:13px;font-weight:500">
                        Nama Petugas Pengambil <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control form-control-sm" id="receivedByName"
                           placeholder="Nama lengkap..." style="font-size:13px" required>
                    <div class="invalid-feedback" id="errorReceivedBy"></div>
                </div>
                <div class="modal-footer border-0 pt-0" style="background:var(--bg-surface)">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                            style="border-radius:var(--radius-md);font-size:13px">Batal</button>
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-2"
                            style="border-radius:var(--radius-md);font-size:13px">
                        <span class="spinner-border spinner-border-sm d-none" id="btnCompleteSpinner"></span>
                        <i class="bi bi-check-lg"></i> Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('requests._scripts_show', ['request' => $request])
@endpush
