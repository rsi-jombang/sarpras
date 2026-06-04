@extends('layouts.app')

@section('title', 'Review Permintaan - ' . $request->request_number)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">Review Permintaan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:12.5px">
                    <li class="breadcrumb-item"><a href="{{ route('requests.index') }}" class="text-decoration-none" style="color:var(--text-muted)">Permintaan</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('requests.show', $request) }}" class="text-decoration-none" style="color:var(--text-muted)">{{ $request->request_number }}</a></li>
                    <li class="breadcrumb-item active" style="color:var(--text-secondary)">Review</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('requests.show', $request) }}" class="btn btn-outline-secondary d-flex align-items-center gap-1"
           style="border-radius:var(--radius-md);font-size:13px">
            <i class="bi bi-arrow-left"></i> Kembali ke Detail
        </a>
    </div>

    {{-- Info Permintaan --}}
    <div class="card border-0 mb-4 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
        <div class="card-body p-4">
            <div class="row g-3" style="font-size:13px">
                <div class="col-md-3">
                    <span class="text-muted d-block" style="font-size:12px">No. Permintaan</span>
                    <span class="fw-bold" style="color:var(--accent-primary)">{{ $request->request_number }}</span>
                </div>
                <div class="col-md-3">
                    <span class="text-muted d-block" style="font-size:12px">Unit</span>
                    <span class="fw-semibold">{{ $request->unit->name }}</span>
                </div>
                <div class="col-md-3">
                    <span class="text-muted d-block" style="font-size:12px">Pengaju</span>
                    <span class="fw-semibold">{{ $request->requester->name }}</span>
                </div>
                <div class="col-md-3">
                    <span class="text-muted d-block" style="font-size:12px">Tanggal Dibutuhkan</span>
                    <span class="fw-semibold {{ $request->needed_date?->isPast() ? 'text-danger' : '' }}">
                        {{ $request->needed_date?->format('d M Y') ?? '—' }}
                    </span>
                </div>
                @if($request->purpose)
                    <div class="col-12">
                        <span class="text-muted d-block" style="font-size:12px">Keperluan</span>
                        <span>{{ $request->purpose }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <form action="{{ route('requests.review', $request) }}" method="POST" id="reviewForm">
        @csrf

        <div class="row g-4">

            {{-- Tabel Review Per Item --}}
            <div class="col-lg-8">
                <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold mb-1" style="color:var(--text-primary)">Daftar Barang — Tentukan Jumlah</h5>
                        <p class="text-muted mb-0" style="font-size:12.5px">
                            Isi jumlah yang disetujui untuk setiap barang. Isi <strong>0</strong> untuk menolak item tersebut.
                        </p>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="table-responsive">
                            <table class="table align-middle" style="font-size:13px">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th class="text-center" width="80">Diminta</th>
                                        <th class="text-center" width="80">Stok</th>
                                        <th class="text-center" width="110">Disetujui <span class="text-danger">*</span></th>
                                        <th width="170">Alasan Tolak</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($request->items as $i => $reqItem)
                                        @php
                                            $totalStock = $reqItem->item->stocks()->sum('quantity');
                                            $stockColor = $totalStock === 0 ? 'danger' : ($totalStock < $reqItem->quantity_requested ? 'warning' : 'success');
                                        @endphp
                                        <tr class="review-row" data-requested="{{ $reqItem->quantity_requested }}">
                                            <input type="hidden" name="items[{{ $i }}][request_item_id]" value="{{ $reqItem->id }}">
                                            <td>
                                                <div class="fw-semibold" style="color:var(--text-primary)">{{ $reqItem->item->name }}</div>
                                                <small class="text-muted">{{ $reqItem->item->code }} · {{ $reqItem->item->category->name }}</small>
                                            </td>
                                            <td class="text-center fw-semibold">
                                                {{ $reqItem->quantity_requested }}
                                                <small class="text-muted d-block" style="font-size:11px">{{ $reqItem->item->unit_of_measure }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold text-{{ $stockColor }}">{{ $totalStock }}</span>
                                                <small class="text-muted d-block" style="font-size:11px">tersedia</small>
                                            </td>
                                            <td class="text-center">
                                                <input type="number"
                                                       class="form-control form-control-sm text-center qty-approved @error('items.'.$i.'.quantity_approved') is-invalid @enderror"
                                                       name="items[{{ $i }}][quantity_approved]"
                                                       value="{{ old('items.'.$i.'.quantity_approved', $reqItem->quantity_requested) }}"
                                                       min="0" max="{{ $reqItem->quantity_requested }}"
                                                       style="width:75px;margin:0 auto;font-size:13px">
                                                @error('items.'.$i.'.quantity_approved')
                                                    <div class="text-danger" style="font-size:11px">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text"
                                                       class="form-control form-control-sm rejection-reason"
                                                       name="items[{{ $i }}][rejection_reason]"
                                                       value="{{ old('items.'.$i.'.rejection_reason') }}"
                                                       placeholder="Jika ditolak..." style="font-size:12px">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Keputusan --}}
            <div class="col-lg-4">
                <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3" style="color:var(--text-secondary);font-size:12px;text-transform:uppercase;letter-spacing:.5px">
                            <i class="bi bi-check2-square me-2"></i>Keputusan Review
                        </h6>

                        {{-- Ringkasan auto-hitung --}}
                        <div class="p-3 rounded-3 mb-3" style="background:var(--bg-muted);border:1px solid var(--border-subtle);font-size:13px">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Total item diminta</span>
                                <span class="fw-semibold">{{ $request->items->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Item disetujui penuh</span>
                                <span class="fw-semibold text-success" id="countFull">0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Item disetujui sebagian</span>
                                <span class="fw-semibold text-info" id="countPartial">0</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Item ditolak</span>
                                <span class="fw-semibold text-danger" id="countRejected">0</span>
                            </div>
                        </div>

                        {{-- Aksi yang akan diambil (auto dari ringkasan) --}}
                        <input type="hidden" name="action" id="reviewAction" value="approve">
                        <div class="mb-3 p-2 rounded-2 text-center" id="actionPreview"
                             style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);font-size:13px">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            <span class="fw-semibold text-success">Akan disetujui penuh</span>
                        </div>

                        {{-- Override: Tolak semua --}}
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="chkRejectAll">
                            <label class="form-check-label" for="chkRejectAll" style="font-size:13px">
                                Tolak seluruh permintaan
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:500">
                                Catatan Review <span id="noteRequired" class="text-danger d-none">*</span>
                            </label>
                            <textarea name="review_notes" id="reviewNotes" rows="3"
                                      class="form-control form-control-sm @error('review_notes') is-invalid @enderror"
                                      placeholder="Catatan untuk unit pengaju..."
                                      style="font-size:13px">{{ old('review_notes') }}</textarea>
                            @error('review_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2"
                                    id="btnSubmitReview"
                                    style="border-radius:var(--radius-md);background:var(--accent-primary);border-color:var(--accent-primary);font-size:13px">
                                <i class="bi bi-send"></i> Kirim Keputusan
                            </button>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
@include('requests._scripts_review', ['request' => $request])
@endpush
