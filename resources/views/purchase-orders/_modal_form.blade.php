{{-- Modal Form: Buat / Edit Purchase Order --}}
<div class="modal fade" id="poModal" tabindex="-1" aria-labelledby="poModalTitle" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0">

            <div class="modal-header border-0 pb-0" style="background: var(--bg-surface)">
                <div>
                    <h5 class="modal-title fw-bold" id="poModalTitle" style="color: var(--text-primary)">Buat Purchase Order</h5>
                    <p class="text-muted mb-0 mt-1" style="font-size: 12.5px" id="poModalSubtitle">Isi informasi pengadaan barang di bawah ini.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="poForm" novalidate>
                @csrf
                <input type="hidden" id="poId">

                <div class="modal-body px-4 py-3" style="background: var(--bg-surface)">

                    {{-- Informasi Umum --}}
                    <div class="p-3 rounded-3 mb-4" style="background: var(--bg-muted); border: 1px solid var(--border-subtle)">
                        <h6 class="fw-bold mb-3" style="color: var(--text-secondary); font-size: 13px; text-transform: uppercase; letter-spacing: .5px">
                            <i class="bi bi-info-circle me-2"></i>Informasi Umum
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 13px; font-weight: 500">Nama Supplier</label>
                                <input type="text" class="form-control form-control-sm" id="supplierName" name="supplier_name"
                                       placeholder="Contoh: PT. Medika Utama" style="font-size: 13px">
                                <div class="invalid-feedback" id="error-supplier_name"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 13px; font-weight: 500">Kontak Supplier</label>
                                <input type="text" class="form-control form-control-sm" id="supplierContact" name="supplier_contact"
                                       placeholder="No. telepon / email supplier" style="font-size: 13px">
                                <div class="invalid-feedback" id="error-supplier_contact"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 13px; font-weight: 500">Tanggal Order</label>
                                <input type="date" class="form-control form-control-sm" id="orderDate" name="order_date" style="font-size: 13px">
                                <div class="invalid-feedback" id="error-order_date"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 13px; font-weight: 500">Estimasi Tanggal Terima</label>
                                <input type="date" class="form-control form-control-sm" id="expectedDate" name="expected_date" style="font-size: 13px">
                                <div class="invalid-feedback" id="error-expected_date"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size: 13px; font-weight: 500">Catatan</label>
                                <textarea class="form-control form-control-sm" id="poNotes" name="notes" rows="2"
                                          placeholder="Catatan tambahan untuk PO ini..." style="font-size: 13px"></textarea>
                                <div class="invalid-feedback" id="error-notes"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Daftar Barang --}}
                    <div class="p-3 rounded-3" style="background: var(--bg-muted); border: 1px solid var(--border-subtle)">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0" style="color: var(--text-secondary); font-size: 13px; text-transform: uppercase; letter-spacing: .5px">
                                <i class="bi bi-box-seam me-2"></i>Daftar Barang
                            </h6>
                            <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                                    id="btnAddItem" style="border-radius: var(--radius-sm); font-size: 12.5px">
                                <i class="bi bi-plus-lg"></i> Tambah Barang
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0" id="itemsTable" style="font-size: 13px">
                                <thead>
                                    <tr style="background: var(--bg-surface)">
                                        <th style="width: 25%">Barang</th>
                                        <th style="width: 10%" class="text-center">Jumlah</th>
                                        <th style="width: 8%" class="text-center">Satuan</th>
                                        <th style="width: 15%">Harga Satuan (Rp)</th>
                                        <th style="width: 20%" class="text-end">Subtotal (Rp)</th>
                                        <th style="width: 10%" class="text-center">Batch / Lot</th>
                                        <th style="width: 10%" class="text-center">Exp. Date</th>
                                        <th style="width: 50px" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                    {{-- rows diisi via JS --}}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold" style="font-size: 13px; color: var(--text-secondary)">Total Keseluruhan</td>
                                        <td class="text-end fw-bold" id="grandTotal" style="font-size: 14px; color: var(--accent-primary)">Rp 0</td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div id="emptyItemsMsg" class="text-center py-4 text-muted" style="font-size: 13px; display: none !important">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                            Belum ada barang. Klik "Tambah Barang" untuk mulai.
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 pt-0" style="background: var(--bg-surface)">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                            style="border-radius: var(--radius-md); font-size: 13px">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" id="btnSavePO"
                            style="border-radius: var(--radius-md); background: var(--accent-primary); border-color: var(--accent-primary); font-size: 13px">
                        <span class="spinner-border spinner-border-sm d-none" role="status" id="btnSpinner"></span>
                        <i class="bi bi-floppy" id="btnSaveIcon"></i>
                        <span id="btnSaveText">Simpan PO</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Template baris item (di-clone via JS) --}}
<template id="itemRowTemplate">
    <tr class="item-row">
        <td>
            <select class="form-select form-select-sm item-select" name="items[__INDEX__][item_id]"
                    style="font-size: 13px" required>
                <option value="">-- Pilih Barang --</option>
            </select>
        </td>
        <td class="text-center">
            <input type="number" class="form-control form-control-sm text-center item-qty" name="items[__INDEX__][quantity_ordered]"
                   min="1" value="1" style="font-size: 13px; width: 70px" required>
        </td>
        <td class="text-center">
            <span class="item-uom text-muted" style="font-size: 12px">—</span>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <span class="input-group-text" style="font-size: 12px">Rp</span>
                <input type="number" class="form-control item-price" name="items[__INDEX__][unit_price]"
                       min="0" value="0" style="font-size: 13px" required>
            </div>
        </td>
        <td class="text-end" style="min-width: 150px">
            <div class="input-group input-group-sm">
                <span class="input-group-text" style="font-size: 12px">Rp</span>
                <input type="number" class="form-control text-end item-subtotal" name="items[__INDEX__][subtotal]"
                       min="0" value="0" style="font-size: 13px">
            </div>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="items[__INDEX__][batch_number]"
                   placeholder="No. batch" style="font-size: 12px">
        </td>
        <td>
            <input type="date" class="form-control form-control-sm" name="items[__INDEX__][expired_date]"
                   style="font-size: 12px">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item"
                    style="border-radius: var(--radius-sm); width: 30px; height: 30px; padding: 0">
                <i class="bi bi-trash" style="font-size: 12px"></i>
            </button>
        </td>
    </tr>
</template>
