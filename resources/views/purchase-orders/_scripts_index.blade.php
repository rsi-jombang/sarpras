<script>
document.addEventListener('DOMContentLoaded', function () {

    // =========================================================================
    // 0. SETUP GLOBAL
    // =========================================================================
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const API     = '/api/purchase-orders';
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
    // 1. DATATABLES
    // =========================================================================
    const table = $('#poTable').DataTable({
        processing:  true,
        serverSide:  true,
        ajax: {
            url: API,
            data: function (d) {
                d.status = $('#filterStatus').val();
            },
        },
        columns: [
            { data: 'DT_RowIndex',         name: 'DT_RowIndex',        orderable: false, searchable: false, className: 'text-center' },
            { data: 'po_number_display',   name: 'po_number',          className: 'fw-bold' },
            { data: 'supplier_display',    name: 'supplier_name' },
            { data: 'order_date_display',  name: 'order_date' },
            { data: 'total_display',       name: 'total_amount',       className: 'text-end' },
            { data: 'status_badge',        name: 'status',             orderable: false, searchable: false, className: 'text-center' },
            { data: 'created_by_display',  name: 'created_by',         orderable: false },
            { data: 'action',              name: 'action',             orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    // Filter status & search
    $('#filterStatus').on('change', () => table.draw());
    $('#tableSearch').on('keyup', function () { table.search($(this).val()).draw(); });

    // =========================================================================
    // 2. LOAD STATS & ITEMS MASTER
    // =========================================================================
    let itemsMaster = [];

    function loadStats() {
        $.get(`${API}/stats`, function (res) {
            if (!res.success) return;
            const map = { total: res.data.total, draft: res.data.draft, ordered: res.data.ordered, received: res.data.received, cancelled: res.data.cancelled };
            Object.entries(map).forEach(([key, val]) => {
                const el = document.getElementById(`stat-${key}`);
                if (el && window.animateCountElement) window.animateCountElement(el, parseInt(val));
                else if (el) el.textContent = val;
            });
        });
    }

    function loadItems() {
        $.get(`${API}/items`, function (res) {
            if (res.success) itemsMaster = res.data;
        });
    }

    loadStats();
    loadItems();

    // =========================================================================
    // 3. FORM MODAL — BUAT / EDIT
    // =========================================================================
    let itemIndex = 0;

    function formatRupiah(val) {
        return 'Rp ' + parseInt(val || 0).toLocaleString('id-ID');
    }

    function recalcTotal() {
        let grand = 0;
        $('#itemsBody .item-row').each(function () {
            const qty   = parseInt($(this).find('.item-qty').val()) || 0;
            const price = parseInt($(this).find('.item-price').val()) || 0;
            const sub   = qty * price;
            $(this).find('.item-subtotal').text(formatRupiah(sub));
            grand += sub;
        });
        $('#grandTotal').text(formatRupiah(grand));
        toggleEmptyMsg();
    }

    function toggleEmptyMsg() {
        const isEmpty = $('#itemsBody .item-row').length === 0;
        $('#emptyItemsMsg').toggle(isEmpty);
        // tbody dan tfoot tetap tampil
    }

    function buildSelectOptions(selectedId = null) {
        let html = '<option value="">-- Pilih Barang --</option>';
        itemsMaster.forEach(item => {
            const sel = selectedId && String(item.id) === String(selectedId) ? 'selected' : '';
            html += `<option value="${item.id}" data-uom="${item.unit_of_measure}" ${sel}>${item.name} (${item.code})</option>`;
        });
        return html;
    }

    function addItemRow(data = {}) {
        const tpl   = document.getElementById('itemRowTemplate').innerHTML.replaceAll('__INDEX__', itemIndex++);
        const $row  = $(tpl);
        $row.find('.item-select').html(buildSelectOptions(data.item_id));
        if (data.item_id) {
            const found = itemsMaster.find(i => String(i.id) === String(data.item_id));
            if (found) $row.find('.item-uom').text(found.unit_of_measure);
        }
        if (data.quantity_ordered)  $row.find('.item-qty').val(data.quantity_ordered);
        if (data.unit_price)        $row.find('.item-price').val(data.unit_price);
        if (data.batch_number)      $row.find('[name*="batch_number"]').val(data.batch_number);
        if (data.expired_date)      $row.find('[name*="expired_date"]').val(data.expired_date?.substring(0, 10));
        $('#itemsBody').append($row);
        recalcTotal();
    }

    // Delegasi event pada baris item
    $('#itemsBody')
        .on('change', '.item-select', function () {
            const opt = $(this).find(':selected');
            $(this).closest('.item-row').find('.item-uom').text(opt.data('uom') || '—');
        })
        .on('input', '.item-qty, .item-price', recalcTotal)
        .on('click', '.btn-remove-item', function () {
            $(this).closest('.item-row').remove();
            recalcTotal();
        });

    $('#btnAddItem').on('click', () => addItemRow());

    // Buka modal tambah baru
    $('#btnCreatePO').on('click', function () {
        $('#poId').val('');
        $('#poForm')[0].reset();
        $('#itemsBody').empty();
        itemIndex = 0;
        recalcTotal();
        $('#poModalTitle').text('Buat Purchase Order');
        $('#poModalSubtitle').text('Isi informasi pengadaan barang di bawah ini.');
        $('#btnSaveText').text('Simpan PO');
        clearFormErrors();
        addItemRow(); // minimal 1 baris
        $('#poModal').modal('show');
    });

    // Buka modal edit
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        $.get(`${API}/${id}`, function (res) {
            if (!res.success) return;
            const po = res.data;
            $('#poId').val(po.id);
            $('#supplierName').val(po.supplier_name);
            $('#supplierContact').val(po.supplier_contact);
            $('#orderDate').val(po.order_date?.substring(0, 10));
            $('#expectedDate').val(po.expected_date?.substring(0, 10));
            $('#poNotes').val(po.notes);
            $('#itemsBody').empty();
            itemIndex = 0;
            po.items.forEach(item => addItemRow({
                item_id:          item.item_id,
                quantity_ordered:  item.quantity_ordered,
                unit_price:       item.unit_price,
                batch_number:     item.batch_number,
                expired_date:     item.expired_date,
            }));
            $('#poModalTitle').text('Edit Purchase Order');
            $('#poModalSubtitle').text('PO ' + po.po_number);
            $('#btnSaveText').text('Perbarui PO');
            clearFormErrors();
            $('#poModal').modal('show');
        });
    });

    // Submit form
    $('#poForm').on('submit', function (e) {
        e.preventDefault();
        clearFormErrors();

        if ($('#itemsBody .item-row').length === 0) {
            Toast.fire({ ...swalCfg(), icon: 'warning', title: 'Minimal satu barang harus ditambahkan.' });
            return;
        }

        const id     = $('#poId').val();
        const isEdit = !!id;
        const url    = isEdit ? `${API}/${id}` : API;
        const method = isEdit ? 'PUT' : 'POST';

        $('#btnSpinner').removeClass('d-none');
        $('#btnSaveIcon').addClass('d-none');
        $('#btnSavePO').prop('disabled', true);

        $.ajax({
            url, type: method, data: $(this).serialize(),
            success: function (res) {
                resetSubmitBtn();
                if (res.success) {
                    $('#poModal').modal('hide');
                    Toast.fire({ ...swalCfg(), icon: 'success', title: res.message });
                    table.ajax.reload(null, false);
                    loadStats();
                }
            },
            error: function (xhr) {
                console.log(xhr.responseJSON);
                resetSubmitBtn();
                if (xhr.status === 422) {
                    showFormErrors(xhr.responseJSON.errors);
                } else {
                    Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Terjadi kesalahan.' });
                }
            },
        });
    });

    // =========================================================================
    // 4. APPROVE
    // =========================================================================
    $(document).on('click', '.btn-approve', function () {
        const id = $(this).data('id');
        Swal.fire({
            ...swalCfg(),
            title: 'Setujui Purchase Order?',
            text: 'PO yang sudah disetujui tidak dapat diedit kembali.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-check-lg"></i> Ya, Setujui',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: 'btn btn-success px-4 py-2 me-2', cancelButton: 'btn btn-outline-secondary px-4 py-2' },
            buttonsStyling: false,
        }).then(result => {
            if (!result.isConfirmed) return;
            $.post(`${API}/${id}/approve`, {}, function (res) {
                if (res.success) {
                    Toast.fire({ ...swalCfg(), icon: 'success', title: res.message });
                    table.ajax.reload(null, false);
                    loadStats();
                }
            }).fail(xhr => {
                Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal menyetujui PO.' });
            });
        });
    });

    // =========================================================================
    // 5. MODAL RECEIVE (dari index)
    // =========================================================================
    $(document).on('click', '.btn-receive', function () {
        openReceiveModal($(this).data('id'));
    });

    // =========================================================================
    // 6. CANCEL
    // =========================================================================
    $(document).on('click', '.btn-cancel', function () {
        const id = $(this).data('id');
        Swal.fire({
            ...swalCfg(),
            title: 'Batalkan Purchase Order?',
            text: 'PO yang dibatalkan tidak dapat diaktifkan kembali.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-x-lg"></i> Ya, Batalkan',
            cancelButtonText: 'Tidak',
            customClass: { confirmButton: 'btn btn-danger px-4 py-2 me-2', cancelButton: 'btn btn-outline-secondary px-4 py-2' },
            buttonsStyling: false,
        }).then(result => {
            if (!result.isConfirmed) return;
            $.post(`${API}/${id}/cancel`, {}, function (res) {
                if (res.success) {
                    Toast.fire({ ...swalCfg(), icon: 'success', title: res.message });
                    table.ajax.reload(null, false);
                    loadStats();
                }
            }).fail(xhr => {
                Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal membatalkan PO.' });
            });
        });
    });

    // =========================================================================
    // 7. FUNGSI RECEIVE (shared antara index dan show)
    // =========================================================================
    window.openReceiveModal = function (poId) {
        $.get(`${API}/${poId}`, function (res) {
            if (!res.success) return;
            const po = res.data;
            $('#receivePOId').val(po.id);
            $('#receiveModalTitle').text('Terima Barang — ' + po.po_number);
            $('#receiveModalSubtitle').text('Supplier: ' + (po.supplier_name || '—'));
            $('#receivedDate').val(new Date().toISOString().substring(0, 10));

            let rows = '';
            po.items.forEach((item, i) => {
                rows += `
                <tr>
                    <td>
                        <div class="fw-semibold" style="font-size:13px">${item.item.name}</div>
                        <small class="text-muted">${item.item.code} · ${item.item.unit_of_measure}</small>
                        <input type="hidden" name="items[${i}][po_item_id]" value="${item.id}">
                    </td>
                    <td class="text-center">${item.quantity_ordered} ${item.item.unit_of_measure}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm text-center"
                               name="items[${i}][quantity_received]" value="${item.quantity_ordered}"
                               min="0" style="font-size:13px; width:80px; margin: 0 auto" required>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text" style="font-size:12px">Rp</span>
                            <input type="number" class="form-control" name="items[${i}][unit_price]"
                                   value="${item.unit_price}" min="0" style="font-size:13px" required>
                        </div>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm"
                               name="items[${i}][batch_number]" value="${item.batch_number || ''}"
                               placeholder="No. batch" style="font-size:12px">
                    </td>
                    <td>
                        <input type="date" class="form-control form-control-sm"
                               name="items[${i}][expired_date]" value="${item.expired_date ? item.expired_date.substring(0,10) : ''}"
                               style="font-size:12px">
                    </td>
                </tr>`;
            });

            $('#receiveItemsBody').html(rows);
            $('#receiveModal').modal('show');
        });
    };

    $('#receiveForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#receivePOId').val();
        $('#btnReceiveSpinner').removeClass('d-none');
        $('#btnConfirmReceive').prop('disabled', true);

        $.ajax({
            url: `${API}/${id}/receive`,
            type: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                $('#btnReceiveSpinner').addClass('d-none');
                $('#btnConfirmReceive').prop('disabled', false);
                if (res.success) {
                    $('#receiveModal').modal('hide');
                    Toast.fire({ ...swalCfg(), icon: 'success', title: res.message });
                    table.ajax.reload(null, false);
                    loadStats();
                }
            },
            error: function (xhr) {
                $('#btnReceiveSpinner').addClass('d-none');
                $('#btnConfirmReceive').prop('disabled', false);
                if (xhr.status === 422) {
                    Toast.fire({ ...swalCfg(), icon: 'error', title: 'Periksa kembali data penerimaan.' });
                } else {
                    Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal memproses penerimaan.' });
                }
            },
        });
    });

    // =========================================================================
    // HELPERS
    // =========================================================================
    function clearFormErrors() {
        $('.form-control, .form-select').removeClass('is-invalid');
        $('[id^="error-"]').text('');
    }

    function showFormErrors(errors) {
        Object.entries(errors).forEach(([key, messages]) => {
            const field = $(`[name="${key}"]`);
            field.addClass('is-invalid');
            $(`#error-${key.replace(/\./g, '_')}`).text(messages[0]);
        });
    }

    function resetSubmitBtn() {
        $('#btnSpinner').addClass('d-none');
        $('#btnSaveIcon').removeClass('d-none');
        $('#btnSavePO').prop('disabled', false);
    }

    toggleEmptyMsg();
});
</script>
