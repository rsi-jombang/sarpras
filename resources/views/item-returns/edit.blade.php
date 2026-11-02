@extends('layouts.app')

@section('title', 'Edit Draft Pengembalian - ' . $itemReturn->return_number)

@section('content')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">Edit Draft Pengembalian</h1>
    </div>

    <form action="{{ route('item-returns.update', $itemReturn) }}" method="POST" id="retForm" novalidate>
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 p-4" style="background:var(--bg-surface);border-radius:var(--radius-lg)">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px">Unit</label>
                        <input type="text" class="form-control form-control-sm" value="{{ $itemReturn->unit->name ?? '—' }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:500">Alasan</label>
                        <select name="reason" class="form-select form-select-sm" style="font-size:13px">
                            <option value="kelebihan" {{ $itemReturn->reason === 'kelebihan' ? 'selected' : '' }}>Kelebihan</option>
                            <option value="salah" {{ $itemReturn->reason === 'salah' ? 'selected' : '' }}>Salah Kirim</option>
                            <option value="expired" {{ $itemReturn->reason === 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                            <option value="lainnya" {{ $itemReturn->reason === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:500">Catatan</label>
                        <textarea name="notes" rows="2" class="form-control form-control-sm">{{ $itemReturn->notes }}</textarea>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card border-0 p-4" style="background:var(--bg-surface);border-radius:var(--radius-lg)">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0" style="font-size:12.5px">Daftar Barang yang Dikembalikan</h6>
                        <button type="button" id="btnAddItem" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus-lg"></i> Tambah</button>
                    </div>
                    <table class="table table-sm">
                        <thead><tr><th>Barang</th><th class="text-center">Stok</th><th class="text-center">Jumlah</th><th class="text-center">Satuan</th><th></th></tr></thead>
                        <tbody id="itemsBody"></tbody>
                    </table>
                    <div id="emptyMsg" class="text-center text-muted py-3">Belum ada barang.</div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('item-returns.show', $itemReturn) }}" class="btn btn-outline-secondary btn-sm">Batal</a>
                        <button type="submit" class="btn btn-primary btn-sm" id="btnSubmit">Perbarui Draft</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <template id="itemRowTpl">
        <tr class="item-row">
            <td>
                <select class="form-select form-select-sm item-select" name="items[__INDEX__][item_id]" required><option value="">-- Pilih Barang --</option></select>
            </td>
            <td class="text-center"><span class="item-stock text-muted">—</span></td>
            <td class="text-center">
                <input type="number" class="form-control form-control-sm item-qty" name="items[__INDEX__][quantity_returned]" min="1" value="1" style="width:70px;margin:0 auto" required>
            </td>
            <td class="text-center item-uom text-muted">—</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove" style="width:30px;height:30px;padding:0"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    </template>
</div>
@push('scripts')
<script>
window.__EXISTING_ITEMS__ = @json($existingItems);
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@include('item-returns._scripts_create')
@endpush
@endsection