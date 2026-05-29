{{-- Modal Penerimaan Barang --}}
<div class="modal fade" id="receiveModal" tabindex="-1" aria-labelledby="receiveModalTitle" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0">

            <div class="modal-header border-0 pb-0" style="background: var(--bg-surface)">
                <div>
                    <h5 class="modal-title fw-bold" id="receiveModalTitle" style="color: var(--text-primary)">Terima Barang</h5>
                    <p class="text-muted mb-0 mt-1" style="font-size: 12.5px" id="receiveModalSubtitle">—</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="receiveForm" novalidate>
                @csrf
                <input type="hidden" id="receivePOId">

                <div class="modal-body px-4 py-3" style="background: var(--bg-surface)">

                    {{-- Tanggal Penerimaan --}}
                    <div class="p-3 rounded-3 mb-4" style="background: var(--bg-muted); border: 1px solid var(--border-subtle)">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 13px; font-weight: 500">
                                    Tanggal Penerimaan <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control form-control-sm" id="receivedDate" name="received_date"
                                       style="font-size: 13px" required>
                                <div class="invalid-feedback" id="error-received_date"></div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label" style="font-size: 13px; font-weight: 500">Catatan Penerimaan</label>
                                <input type="text" class="form-control form-control-sm" name="notes"
                                       placeholder="Kondisi barang, keterangan pengiriman, dll." style="font-size: 13px">
                            </div>
                        </div>
                    </div>

                    {{-- Tabel Barang Diterima --}}
                    <div class="p-3 rounded-3" style="background: var(--bg-muted); border: 1px solid var(--border-subtle)">
                        <h6 class="fw-bold mb-3" style="color: var(--text-secondary); font-size: 13px; text-transform: uppercase; letter-spacing: .5px">
                            <i class="bi bi-list-check me-2"></i>Konfirmasi Jumlah Barang Diterima
                        </h6>
                        <p class="text-muted mb-3" style="font-size: 12.5px">
                            Isi jumlah aktual barang yang diterima. Jumlah diterima boleh berbeda dari yang dipesan.
                            Setiap baris akan membentuk <strong>batch stok baru</strong> di sistem.
                        </p>

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0" style="font-size: 13px">
                                <thead>
                                    <tr style="background: var(--bg-surface)">
                                        <th>Barang</th>
                                        <th class="text-center">Dipesan</th>
                                        <th class="text-center" style="width: 100px">Diterima <span class="text-danger">*</span></th>
                                        <th style="width: 160px">Harga Aktual (Rp)</th>
                                        <th style="width: 110px">Batch / Lot</th>
                                        <th style="width: 140px">Tgl. Kadaluarsa</th>
                                    </tr>
                                </thead>
                                <tbody id="receiveItemsBody">
                                    {{-- Diisi via JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 pt-0" style="background: var(--bg-surface)">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                            style="border-radius: var(--radius-md); font-size: 13px">Batal</button>
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-2" id="btnConfirmReceive"
                            style="border-radius: var(--radius-md); font-size: 13px">
                        <span class="spinner-border spinner-border-sm d-none" role="status" id="btnReceiveSpinner"></span>
                        <i class="bi bi-box-arrow-in-down"></i>
                        <span>Konfirmasi Penerimaan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
