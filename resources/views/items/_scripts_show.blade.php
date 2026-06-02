<script>
document.addEventListener('DOMContentLoaded', function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const ITEM_ID = {{ $item->id }};
    const API     = '/api/items';
    const isDark  = () => document.documentElement.getAttribute('data-theme') === 'dark';
    const swalCfg = () => ({
        background: isDark() ? '#111318' : '#ffffff',
        color:      isDark() ? '#f1f5f9' : '#0f172a',
        confirmButtonColor: '#4f46e5',
        cancelButtonColor:  '#64748b',
    });
    const Toast = Swal.mixin({
        toast: true, position: 'top-end',
        showConfirmButton: false, timer: 3000, timerProgressBar: true,
    });

    // =========================================================================
    // 1. LOAD BATCH STOK
    // =========================================================================
    function loadStocks() {
        $('#stocksLoading').show();
        $.get(`${API}/${ITEM_ID}/stocks`, res => {
            $('#stocksLoading').hide();
            if (!res.success) return;

            const stocks = res.data;
            const uom    = '{{ $item->unit_of_measure }}';

            if (stocks.length === 0) {
                $('#stocksContainer').html(`
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        <span style="font-size:13px">Belum ada stok untuk barang ini.</span>
                    </div>`);
                $('#totalStockDisplay').text('0 ' + uom);
                $('#totalBatchDisplay').text('0');
                return;
            }

            let totalStock = 0;
            let activeBatch = 0;
            let html = '<div class="row g-3">';

            stocks.forEach((s, i) => {
                totalStock += s.quantity;
                if (s.quantity > 0) activeBatch++;

                const stockColor = s.quantity === 0 ? 'danger' : (s.is_low_stock ? 'warning' : 'success');
                const expiredBadge = s.is_expired
                    ? '<span class="badge bg-danger-subtle text-danger ms-1" style="font-size:10px">Kadaluarsa</span>'
                    : s.is_expiring_soon
                        ? '<span class="badge bg-warning-subtle text-warning ms-1" style="font-size:10px">Hampir Exp.</span>'
                        : '';
                const lowBadge = s.is_low_stock && s.quantity > 0
                    ? '<span class="badge bg-warning-subtle text-warning ms-1" style="font-size:10px">Stok Rendah</span>'
                    : '';

                html += `
                <div class="col-md-6">
                    <div class="p-3 rounded-3 h-100" style="background:var(--bg-muted);border:1px solid var(--border-subtle);font-size:13px">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold" style="color:var(--text-secondary);font-size:11px;text-transform:uppercase;letter-spacing:.5px">
                                Batch #${i + 1}
                            </span>
                            <div>${expiredBadge}${lowBadge}</div>
                        </div>
                        <div class="d-flex align-items-end gap-1 mb-3">
                            <span class="fw-bold text-${stockColor}" style="font-size:1.6rem;line-height:1">${s.quantity}</span>
                            <span class="text-muted mb-1" style="font-size:12px">${uom}</span>
                        </div>
                        <div class="row g-1" style="font-size:12px">
                            <div class="col-6">
                                <span class="text-muted">Harga Beli</span>
                                <div class="fw-semibold" style="color:var(--text-primary)">
                                    Rp ${parseInt(s.purchase_price).toLocaleString('id-ID')}
                                </div>
                            </div>
                            <div class="col-6">
                                <span class="text-muted">Min. Stok</span>
                                <div class="fw-semibold" style="color:var(--text-primary)">${s.minimum_stock} ${uom}</div>
                            </div>
                            <div class="col-6 mt-2">
                                <span class="text-muted">Batch / Lot</span>
                                <div class="fw-semibold" style="color:var(--text-primary)">${s.batch_number}</div>
                            </div>
                            <div class="col-6 mt-2">
                                <span class="text-muted">Tgl. Kadaluarsa</span>
                                <div class="fw-semibold ${s.is_expired ? 'text-danger' : ''}" style="color:var(--text-primary)">${s.expired_date}</div>
                            </div>
                            <div class="col-12 mt-2">
                                <span class="text-muted">Masuk Sejak</span>
                                <div class="fw-semibold" style="color:var(--text-primary)">${s.created_at}</div>
                            </div>
                        </div>
                    </div>
                </div>`;
            });

            html += '</div>';
            $('#stocksContainer').html(html);
            $('#totalStockDisplay').text(totalStock + ' ' + uom);
            $('#totalBatchDisplay').text(activeBatch);
        });
    }

    loadStocks();

    // =========================================================================
    // 2. DATATABLES — RIWAYAT PERGERAKAN
    // =========================================================================
    const movTable = $('#movementsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: `${API}/${ITEM_ID}/movements` },
        columns: [
            { data: 'DT_RowIndex',       name: 'DT_RowIndex',    orderable: false, searchable: false, className: 'text-center' },
            { data: 'type_badge',        name: 'movement_type',  orderable: false },
            { data: 'qty_display',       name: 'quantity',       className: 'text-center' },
            { data: 'stock_change',      name: 'stock_after',    orderable: false },
            { data: 'batch_display',     name: 'batch',          orderable: false },
            { data: 'notes',             name: 'notes',          orderable: false, defaultContent: '<em class="text-muted">—</em>' },
            { data: 'performer_display', name: 'performed_by',   orderable: false },
            { data: 'date_display',      name: 'created_at' },
        ],
        order: [[7, 'desc']],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
    });

    // Reload DataTables saat tab movement aktif
    $('button[data-bs-target="#tabMovements"]').on('shown.bs.tab', function () {
        movTable.columns.adjust().draw();
    });

    // =========================================================================
    // 3. EDIT BARANG (reuse modal & logic dari index)
    // =========================================================================
    let categoriesMaster = [];

    function loadCategories(callback) {
        $.get(`${API}/categories`, res => {
            if (!res.success) return;
            categoriesMaster = res.data;
            let opts = '<option value="">-- Pilih Kategori --</option>';
            res.data.forEach(c => opts += `<option value="${c.id}" data-type="${c.item_type}">${c.name}</option>`);
            $('#itemCategory').html(opts);
            if (callback) callback();
        });
    }

    $('#btnEditItem').on('click', function () {
        $.get(`${API}/${ITEM_ID}`, res => {
            if (!res.success) return;
            const item = res.data;
            $('#itemId').val(item.id);
            $('#itemName').val(item.name);
            $('#itemCode').val(item.code);
            $('#itemUom').val(item.unit_of_measure);
            $('#itemType').val(item.item_type);
            $('#itemDesc').val(item.description);
            $('#itemActive').prop('checked', item.is_active);

            loadCategories(() => {
                $('#itemCategory').val(item.category_id);
            });

            if (item.image_path) {
                $('#imagePreview').attr('src', `/storage/${item.image_path}`);
                $('#imagePreviewWrap').show();
            } else {
                $('#imagePreviewWrap').hide();
            }

            $('[id^="error-"]').text('');
            $('.form-control,.form-select').removeClass('is-invalid');
            $('#itemModalTitle').text('Edit Barang');
            $('#btnItemText').text('Perbarui');
            $('#itemModal').modal('show');
        });
    });

    $('#itemImage').on('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            $('#imagePreview').attr('src', e.target.result);
            $('#imagePreviewWrap').show();
        };
        reader.readAsDataURL(file);
    });

    $('#btnRemoveImage').on('click', function () {
        $('#itemImage').val('');
        $('#imagePreview').attr('src', '');
        $('#imagePreviewWrap').hide();
    });

    $('#itemCategory').on('change', function () {
        const opt = $(this).find(':selected');
        if (opt.data('type')) $('#itemType').val(opt.data('type'));
    });

    $('#itemForm').on('submit', function (e) {
        e.preventDefault();
        $('[id^="error-"]').text('');
        $('.form-control,.form-select').removeClass('is-invalid');

        const id       = $('#itemId').val();
        const formData = new FormData(this);
        formData.append('_method', 'POST');

        $('#btnItemSpinner').removeClass('d-none');
        $('#btnItemIcon').addClass('d-none');
        $('#btnSaveItem').prop('disabled', true);

        $.ajax({
            url: `${API}/${id}`, type: 'POST',
            data: formData, processData: false, contentType: false,
            success: res => {
                $('#btnItemSpinner').addClass('d-none');
                $('#btnItemIcon').removeClass('d-none');
                $('#btnSaveItem').prop('disabled', false);
                if (res.success) {
                    $('#itemModal').modal('hide');
                    Toast.fire({ ...swalCfg(), icon: 'success', title: res.message });
                    setTimeout(() => location.reload(), 1200);
                }
            },
            error: xhr => {
                $('#btnItemSpinner').addClass('d-none');
                $('#btnItemIcon').removeClass('d-none');
                $('#btnSaveItem').prop('disabled', false);
                if (xhr.status === 422) {
                    Object.entries(xhr.responseJSON.errors).forEach(([key, msgs]) => {
                        $(`[name="${key}"]`).addClass('is-invalid');
                        $(`#error-${key}`).text(msgs[0]);
                    });
                } else {
                    Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Terjadi kesalahan.' });
                }
            },
        });
    });

});
</script>
