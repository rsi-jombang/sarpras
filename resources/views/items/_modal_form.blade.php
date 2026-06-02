{{-- Modal Form: Tambah / Edit Barang --}}
<div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalTitle" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">

            <div class="modal-header border-0 pb-0" style="background:var(--bg-surface)">
                <div>
                    <h5 class="modal-title fw-bold" id="itemModalTitle" style="color:var(--text-primary)">Tambah Barang</h5>
                    <p class="text-muted mb-0 mt-1" style="font-size:12.5px">Isi data barang di bawah ini.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="itemForm" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" id="itemId">

                <div class="modal-body px-4 py-3" style="background:var(--bg-surface)">
                    <div class="row g-3">

                        {{-- Preview Gambar --}}
                        <div class="col-12 d-flex justify-content-center mb-2">
                            <div id="imagePreviewWrap" class="position-relative" style="display:none!important">
                                <img id="imagePreview" src="" alt="Preview"
                                     style="width:100px;height:100px;object-fit:cover;border-radius:var(--radius-lg);border:2px solid var(--border-subtle)">
                                <button type="button" id="btnRemoveImage"
                                        class="btn btn-sm btn-danger position-absolute"
                                        style="top:-8px;right:-8px;width:24px;height:24px;padding:0;border-radius:50%;font-size:11px">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="font-size:13px;font-weight:500">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="itemName" name="name"
                                   placeholder="Contoh: Spuit 3 ml" style="font-size:13px" required>
                            <div class="invalid-feedback" id="error-name"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="font-size:13px;font-weight:500">Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="itemCode" name="code"
                                   placeholder="Contoh: ALKES-002" style="font-size:13px;text-transform:uppercase" required>
                            <div class="invalid-feedback" id="error-code"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="font-size:13px;font-weight:500">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="itemCategory" name="category_id" style="font-size:13px" required>
                                <option value="">-- Pilih Kategori --</option>
                            </select>
                            <div class="invalid-feedback" id="error-category_id"></div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" style="font-size:13px;font-weight:500">Tipe Barang <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="itemType" name="item_type" style="font-size:13px" required>
                                <option value="">-- Pilih --</option>
                                <option value="consumable">Habis Pakai</option>
                                <option value="inventory">Inventaris</option>
                            </select>
                            <div class="invalid-feedback" id="error-item_type"></div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" style="font-size:13px;font-weight:500">Satuan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="itemUom" name="unit_of_measure"
                                   placeholder="pcs, box, roll..." style="font-size:13px" required>
                            <div class="invalid-feedback" id="error-unit_of_measure"></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" style="font-size:13px;font-weight:500">Deskripsi</label>
                            <textarea class="form-control form-control-sm" id="itemDesc" name="description" rows="2"
                                      placeholder="Keterangan tambahan tentang barang ini..." style="font-size:13px"></textarea>
                            <div class="invalid-feedback" id="error-description"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="font-size:13px;font-weight:500">Gambar Barang</label>
                            <input type="file" class="form-control form-control-sm" id="itemImage" name="image"
                                   accept="image/*" style="font-size:13px">
                            <div class="invalid-feedback" id="error-image"></div>
                            <small class="text-muted" style="font-size:11.5px">Format: JPG, PNG, WebP. Maks. 2MB.</small>
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="itemActive" name="is_active" value="1" checked>
                                <label class="form-check-label" for="itemActive" style="font-size:13px">Barang Aktif</label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-0 pt-0" style="background:var(--bg-surface)">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                            style="border-radius:var(--radius-md);font-size:13px">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" id="btnSaveItem"
                            style="border-radius:var(--radius-md);background:var(--accent-primary);border-color:var(--accent-primary);font-size:13px">
                        <span class="spinner-border spinner-border-sm d-none" id="btnItemSpinner"></span>
                        <i class="bi bi-floppy" id="btnItemIcon"></i>
                        <span id="btnItemText">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
