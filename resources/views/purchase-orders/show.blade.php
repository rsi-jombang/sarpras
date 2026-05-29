@extends('layouts.app')

@section('title', 'Detail PO - ' . $purchaseOrder->po_number)

@section('content')
    <div class="container-fluid px-4 py-4">

        {{-- Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
            <div>
                <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary)">{{ $purchaseOrder->po_number }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="font-size: 12.5px">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-decoration-none" style="color: var(--text-muted)">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('purchase-orders.index') }}" class="text-decoration-none" style="color: var(--text-muted)">Purchase Order</a>
                        </li>
                        <li class="breadcrumb-item active" style="color: var(--text-secondary)">{{ $purchaseOrder->po_number }}</li>
                    </ol>
                </nav>
            </div>

            {{-- Tombol Aksi --}}
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($purchaseOrder->isEditable() && auth()->user()->canCreatePO())
                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}"
                       class="btn btn-sm btn-outline-warning d-flex align-items-center gap-1"
                       style="border-radius: var(--radius-md); font-size: 13px">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                @endif

                @if($purchaseOrder->status->value === 'draft' && auth()->user()->isAdmin())
                    <button type="button" class="btn btn-sm btn-success d-flex align-items-center gap-1"
                            id="btnApprovePO" style="border-radius: var(--radius-md); font-size: 13px">
                        <i class="bi bi-check-lg"></i> Setujui PO
                    </button>
                @endif

                @if($purchaseOrder->canBeReceived() && auth()->user()->canCreatePO())
                    <button type="button" class="btn btn-sm btn-info d-flex align-items-center gap-1 text-white"
                            id="btnReceivePO" style="border-radius: var(--radius-md); font-size: 13px">
                        <i class="bi bi-box-arrow-in-down"></i> Terima Barang
                    </button>
                @endif

                @if(!in_array($purchaseOrder->status->value, ['received', 'cancelled']) && auth()->user()->isAdmin())
                    <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1"
                            id="btnCancelPO" style="border-radius: var(--radius-md); font-size: 13px">
                        <i class="bi bi-x-lg"></i> Batalkan
                    </button>
                @endif
            </div>
        </div>

        <div class="row g-4">

            {{-- Kolom Kiri: Info PO --}}
            <div class="col-lg-4">

                {{-- Kartu Status --}}
                <div class="card border-0 mb-4 animate-in" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm)">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 class="fw-bold mb-0" style="color: var(--text-secondary); font-size: 12px; text-transform: uppercase; letter-spacing: .5px">Status</h6>
                            @php
                                $statusMap = [
                                    'draft'     => ['secondary', 'Draft'],
                                    'approved'  => ['info',      'Disetujui'],
                                    'ordered'   => ['warning',   'Dipesan'],
                                    'received'  => ['success',   'Diterima'],
                                    'cancelled' => ['danger',    'Dibatalkan'],
                                ];
                                [$color, $label] = $statusMap[$purchaseOrder->status->value] ?? ['secondary', '—'];
                            @endphp
                            <span class="badge rounded-pill bg-{{ $color }}-subtle text-{{ $color }} px-3 py-2" style="font-size: 12.5px">
                                {{ $label }}
                            </span>
                        </div>

                        {{-- Timeline Status --}}
                        <div class="d-flex flex-column gap-2 mt-3" style="font-size: 12.5px">
                            @foreach([
                                ['draft',     'bi-pencil-square',      'Dibuat',    $purchaseOrder->created_at],
                                ['approved',  'bi-check-circle',       'Disetujui', $purchaseOrder->updated_at],
                                ['ordered',   'bi-truck',              'Dipesan',   $purchaseOrder->order_date],
                                ['received',  'bi-box-seam',           'Diterima',  $purchaseOrder->received_date],
                            ] as [$s, $icon, $lbl, $date])
                                @php
                                    $statuses = ['draft','approved','ordered','received','cancelled'];
                                    $current  = $purchaseOrder->status->value;
                                    $isActive = $current === $s;
                                    $isDone   = array_search($current, $statuses) > array_search($s, $statuses);
                                @endphp
                                <div class="d-flex align-items-center gap-2 {{ $isActive || $isDone ? '' : 'opacity-40' }}">
                                    <i class="bi {{ $icon }} {{ $isDone ? 'text-success' : ($isActive ? 'text-primary' : 'text-muted') }}"></i>
                                    <span class="{{ $isActive ? 'fw-semibold' : '' }}" style="color: var(--text-primary)">{{ $lbl }}</span>
                                    @if($date)
                                        <span class="ms-auto text-muted">
                                            {{ $date instanceof \Carbon\Carbon ? $date->format('d M Y') : \Carbon\Carbon::parse($date)->format('d M Y') }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Kartu Info Umum --}}
                <div class="card border-0 mb-4 animate-in" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm)">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3" style="color: var(--text-secondary); font-size: 12px; text-transform: uppercase; letter-spacing: .5px">Informasi PO</h6>

                        @foreach([
                            ['bi-hash',              'Nomor PO',          $purchaseOrder->po_number],
                            ['bi-building',          'Supplier',          $purchaseOrder->supplier_name ?? '—'],
                            ['bi-telephone',         'Kontak',            $purchaseOrder->supplier_contact ?? '—'],
                            ['bi-calendar3',         'Tgl. Order',        $purchaseOrder->order_date?->format('d M Y') ?? '—'],
                            ['bi-calendar-check',    'Estimasi Terima',   $purchaseOrder->expected_date?->format('d M Y') ?? '—'],
                            ['bi-box-arrow-in-down', 'Tgl. Diterima',     $purchaseOrder->received_date?->format('d M Y') ?? '—'],
                            ['bi-person',            'Dibuat Oleh',       $purchaseOrder->creator->name ?? '—'],
                            ['bi-person-check',      'Disetujui Oleh',    $purchaseOrder->approver?->name ?? '—'],
                        ] as [$icon, $label, $value])
                            <div class="d-flex gap-2 mb-2 align-items-start" style="font-size: 13px">
                                <i class="bi {{ $icon }} text-muted mt-1" style="font-size: 13px; width: 16px; flex-shrink: 0"></i>
                                <span class="text-muted" style="min-width: 110px; flex-shrink: 0">{{ $label }}</span>
                                <span class="fw-medium" style="color: var(--text-primary)">{{ $value }}</span>
                            </div>
                        @endforeach

                        @if($purchaseOrder->notes)
                            <div class="mt-3 p-2 rounded-2" style="background: var(--bg-muted); font-size: 12.5px; color: var(--text-secondary)">
                                <i class="bi bi-chat-left-text me-1"></i> {{ $purchaseOrder->notes }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Total Nilai --}}
                <div class="card border-0 animate-in" style="background: var(--accent-primary); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm)">
                    <div class="card-body p-4">
                        <p class="mb-1 text-white opacity-75" style="font-size: 12.5px">Total Nilai PO</p>
                        <h3 class="fw-bold text-white mb-0">Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}</h3>
                        <p class="text-white opacity-75 mb-0 mt-1" style="font-size: 12px">
                            {{ $purchaseOrder->items->count() }} jenis barang
                        </p>
                    </div>
                </div>

            </div>

            {{-- Kolom Kanan: Daftar Barang --}}
            <div class="col-lg-8">
                <div class="card border-0 animate-in" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm)">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold mb-1" style="color: var(--text-primary)">Daftar Barang</h5>
                        <p class="text-muted mb-0" style="font-size: 12.5px">Rincian barang dalam Purchase Order ini.</p>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" style="font-size: 13px">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th class="text-center">Dipesan</th>
                                        <th class="text-center">Diterima</th>
                                        <th class="text-end">Harga Satuan</th>
                                        <th class="text-end">Subtotal</th>
                                        <th>Batch</th>
                                        <th>Exp. Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseOrder->items as $item)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold" style="color: var(--text-primary)">{{ $item->item->name }}</div>
                                                <small class="text-muted">{{ $item->item->code }} · {{ $item->item->category->name }}</small>
                                            </td>
                                            <td class="text-center">
                                                {{ $item->quantity_ordered }} <small class="text-muted">{{ $item->item->unit_of_measure }}</small>
                                            </td>
                                            <td class="text-center">
                                                @if($purchaseOrder->status->value === 'received')
                                                    <span class="fw-semibold text-success">{{ $item->quantity_received }}</span>
                                                    <small class="text-muted">{{ $item->item->unit_of_measure }}</small>
                                                @else
                                                    <em class="text-muted">—</em>
                                                @endif
                                            </td>
                                            <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                            <td class="text-end fw-semibold" style="color: var(--accent-primary)">
                                                Rp {{ number_format($item->quantity_ordered * $item->unit_price, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                {{ $item->batch_number ?? '—' }}
                                            </td>
                                            <td>
                                                @if($item->expired_date)
                                                    <span class="{{ $item->expired_date->isPast() ? 'text-danger' : '' }}">
                                                        {{ $item->expired_date->format('d M Y') }}
                                                    </span>
                                                @else
                                                    <em class="text-muted">—</em>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold" style="color: var(--text-secondary)">Total</td>
                                        <td class="text-end fw-bold" style="color: var(--accent-primary); font-size: 14px">
                                            Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Receive --}}
    @include('purchase-orders._modal_receive')
@endsection

@push('scripts')
    @include('purchase-orders._scripts_show')
@endpush
