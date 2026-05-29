<script>
document.addEventListener('DOMContentLoaded', function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const PO_ID  = {{ $purchaseOrder->id }};
    const API    = '/api/purchase-orders';
    const isDark = () => document.documentElement.getAttribute('data-theme') === 'dark';
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

    // -------------------------------------------------------------------------
    // Approve
    // -------------------------------------------------------------------------
    $('#btnApprovePO').on('click', function () {
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
            $.post(`${API}/${PO_ID}/approve`, {}, function (res) {
                if (res.success) {
                    Toast.fire({ ...swalCfg(), icon: 'success', title: res.message });
                    setTimeout(() => location.reload(), 1200);
                }
            }).fail(xhr => {
                Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal menyetujui PO.' });
            });
        });
    });

    // -------------------------------------------------------------------------
    // Receive — buka modal
    // -------------------------------------------------------------------------
    $('#btnReceivePO').on('click', function () {
        if (window.openReceiveModal) {
            window.openReceiveModal(PO_ID);
        }
    });

    // Submit receive (reuse fungsi dari _scripts_index jika keduanya di-load,
    // tapi di show.blade.php kita load standalone)
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
                    setTimeout(() => location.reload(), 1200);
                }
            },
            error: function (xhr) {
                $('#btnReceiveSpinner').addClass('d-none');
                $('#btnConfirmReceive').prop('disabled', false);
                Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal memproses penerimaan.' });
            },
        });
    });

    // Isi modal receive saat dibuka dari halaman show
    $('#btnReceivePO').on('click', function () {
        $.get(`${API}/${PO_ID}`, function (res) {
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
                               min="0" style="font-size:13px; width:80px; margin:0 auto" required>
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
    });

    // -------------------------------------------------------------------------
    // Cancel
    // -------------------------------------------------------------------------
    $('#btnCancelPO').on('click', function () {
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
            $.post(`${API}/${PO_ID}/cancel`, {}, function (res) {
                if (res.success) {
                    Toast.fire({ ...swalCfg(), icon: 'success', title: res.message });
                    setTimeout(() => location.reload(), 1200);
                }
            }).fail(xhr => {
                Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal membatalkan PO.' });
            });
        });
    });

});
</script>
