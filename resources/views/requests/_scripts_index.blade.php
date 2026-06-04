<script>
document.addEventListener('DOMContentLoaded', function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const API    = '/api/requests';
    const isUnit = {{ auth()->user()->isUnit() ? 'true' : 'false' }};
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

    // =========================================================================
    // Columns — disesuaikan per role
    // =========================================================================
    const columns = [
        { data: 'DT_RowIndex',              name: 'DT_RowIndex',    orderable: false, searchable: false, className: 'text-center' },
        { data: 'request_number_display',   name: 'request_number' },
        ...(!isUnit ? [{ data: 'unit_display', name: 'unit_id', orderable: false }] : []),
        { data: 'requester_display',        name: 'requested_by',   orderable: false },
        { data: 'priority_badge',           name: 'priority',       orderable: false, className: 'text-center' },
        { data: 'status_badge',             name: 'status',         orderable: false, className: 'text-center' },
        { data: 'request_date_display',     name: 'request_date' },
        { data: 'needed_date_display',      name: 'needed_date',    orderable: false },
        { data: 'action',                   name: 'action',         orderable: false, searchable: false, className: 'text-end' },
    ];

    // =========================================================================
    // DataTables
    // =========================================================================
    const table = $('#requestTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: API,
            data: d => {
                d.status    = $('#filterStatus').val();
                d.unit_id   = $('#filterUnit').val();
                d.date_from = $('#filterDateFrom').val();
                d.date_to   = $('#filterDateTo').val();
            },
        },
        columns,
        order: [[0, 'desc']],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
    });

    $('#filterStatus, #filterUnit, #filterDateFrom, #filterDateTo').on('change', () => table.draw());
    $('#tableSearch').on('keyup', function () { table.search($(this).val()).draw(); });

    // =========================================================================
    // Stats
    // =========================================================================
    function loadStats() {
        $.get(`${API}/stats`, res => {
            if (!res.success) return;
            ['total','pending','approved','ready','completed'].forEach(key => {
                const el = document.getElementById(`stat-${key}`);
                if (el) el.textContent = res.data[key] ?? 0;
            });
        });
    }
    loadStats();

    // =========================================================================
    // Load Units untuk filter (sarpras/admin)
    // =========================================================================
    if (!isUnit) {
        $.get(`${API}/units`, res => {
            if (!res.success) return;
            let opts = '<option value="">Semua Unit</option>';
            res.data.forEach(u => opts += `<option value="${u.id}">${u.name}</option>`);
            $('#filterUnit').html(opts);
        });
    }

    // =========================================================================
    // Fulfill — sarpras siapkan barang
    // =========================================================================
    $(document).on('click', '.btn-fulfill', function () {
        const id = $(this).data('id');
        Swal.fire({
            ...swalCfg(),
            title: 'Siapkan Barang?',
            text: 'Stok akan dikurangi dan status berubah menjadi "Siap Diambil".',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-box-arrow-in-down"></i> Ya, Siapkan',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: 'btn btn-info text-white px-4 py-2 me-2', cancelButton: 'btn btn-outline-secondary px-4 py-2' },
            buttonsStyling: false,
        }).then(res => {
            if (!res.isConfirmed) return;
            $.post(`${API}/${id}/fulfill`, {}, data => {
                if (data.success) {
                    Toast.fire({ ...swalCfg(), icon: 'success', title: data.message });
                    table.ajax.reload(null, false);
                    loadStats();
                }
            }).fail(xhr => {
                Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal menyiapkan barang.' });
            });
        });
    });

    // =========================================================================
    // Complete — unit konfirmasi ambil
    // =========================================================================
    $(document).on('click', '.btn-complete', function () {
        $('#completeRequestId').val($(this).data('id'));
        $('#receivedByName').val('');
        $('#completeModal').modal('show');
    });

    $('#completeForm').on('submit', function (e) {
        e.preventDefault();
        const id   = $('#completeRequestId').val();
        const name = $('#receivedByName').val().trim();

        if (!name) {
            $('#receivedByName').addClass('is-invalid');
            return;
        }
        $('#receivedByName').removeClass('is-invalid');
        $('#btnCompleteSpinner').removeClass('d-none');

        $.post(`${API}/${id}/complete`, { received_by_name: name }, data => {
            $('#btnCompleteSpinner').addClass('d-none');
            if (data.success) {
                $('#completeModal').modal('hide');
                Toast.fire({ ...swalCfg(), icon: 'success', title: data.message });
                table.ajax.reload(null, false);
                loadStats();
            }
        }).fail(xhr => {
            $('#btnCompleteSpinner').addClass('d-none');
            Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal konfirmasi.' });
        });
    });

    // =========================================================================
    // Cancel — unit batalkan
    // =========================================================================
    $(document).on('click', '.btn-cancel', function () {
        const id = $(this).data('id');
        const no = $(this).data('no');
        Swal.fire({
            ...swalCfg(),
            title: 'Batalkan Permintaan?',
            html: `Permintaan <strong>${no}</strong> akan dibatalkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-x-lg"></i> Ya, Batalkan',
            cancelButtonText: 'Tidak',
            customClass: { confirmButton: 'btn btn-danger px-4 py-2 me-2', cancelButton: 'btn btn-outline-secondary px-4 py-2' },
            buttonsStyling: false,
        }).then(res => {
            if (!res.isConfirmed) return;
            $.post(`${API}/${id}/cancel`, {}, data => {
                if (data.success) {
                    Toast.fire({ ...swalCfg(), icon: 'success', title: data.message });
                    table.ajax.reload(null, false);
                    loadStats();
                }
            }).fail(xhr => {
                Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal membatalkan.' });
            });
        });
    });

});
</script>
