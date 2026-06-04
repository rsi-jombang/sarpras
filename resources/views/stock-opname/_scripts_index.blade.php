<script>
document.addEventListener('DOMContentLoaded', function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const API    = '/api/stock-opname';
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
    // Stats
    // =========================================================================
    function loadStats() {
        $.get(`${API}/stats`, res => {
            if (!res.success) return;
            ['total','open','in_progress','closed'].forEach(key => {
                const el = document.getElementById(`stat-${key.replace('_','-')}`);
                if (el) el.textContent = res.data[key] ?? 0;
            });
            const active = (res.data.open ?? 0) + (res.data.in_progress ?? 0);
            $('#badgeActive').text(active);
            $('#badgeHistory').text(res.data.closed ?? 0);
        });
    }
    loadStats();

    // =========================================================================
    // DataTables — Aktif
    // =========================================================================
    const activeTable = $('#activeTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: API },
        columns: [
            { data: 'DT_RowIndex',            name: 'DT_RowIndex',    orderable: false, searchable: false, className: 'text-center' },
            { data: 'opname_number_display',   name: 'opname_number' },
            { data: 'opname_date_display',     name: 'opname_date' },
            { data: 'status_badge',            name: 'status',         orderable: false, className: 'text-center' },
            { data: 'progress_display',        name: 'progress',       orderable: false },
            { data: 'created_by_display',      name: 'created_by',     orderable: false },
            { data: 'action',                  name: 'action',         orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[2, 'desc']],
    });

    // =========================================================================
    // DataTables — Riwayat (lazy load saat tab diklik)
    // =========================================================================
    let historyTableInitialized = false;
    let historyTable;

    $('button[data-bs-target="#tabHistory"]').on('shown.bs.tab', function () {
        if (historyTableInitialized) {
            historyTable.columns.adjust().draw(false);
            return;
        }

        historyTable = $('#historyTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: { url: `${API}/history` },
            columns: [
                { data: 'DT_RowIndex',            name: 'DT_RowIndex',   orderable: false, searchable: false, className: 'text-center' },
                { data: 'opname_number_display',   name: 'opname_number' },
                { data: 'opname_date_display',     name: 'opname_date' },
                { data: 'closed_date_display',     name: 'closed_date' },
                { data: 'diff_summary',            name: 'diff',          orderable: false },
                { data: 'created_by_display',      name: 'created_by',    orderable: false },
                { data: 'closed_by_display',       name: 'closed_by',     orderable: false },
                { data: 'action',                  name: 'action',        orderable: false, searchable: false, className: 'text-end' },
            ],
            order: [[3, 'desc']],
        });

        $('#historySearch').on('keyup', function () {
            historyTable.search($(this).val()).draw();
        });

        historyTableInitialized = true;
    });

    // =========================================================================
    // Delete opname (open saja)
    // =========================================================================
    $(document).on('click', '.btn-delete-opname', function () {
        const id = $(this).data('id');
        const no = $(this).data('no');

        Swal.fire({
            ...swalCfg(),
            title: 'Hapus Opname?',
            html: `Opname <strong>${no}</strong> dan semua data itemnya akan dihapus.<br>
                   <small class="text-muted">Stok tidak akan terpengaruh karena opname belum ditutup.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: 'btn btn-danger px-4 py-2 me-2', cancelButton: 'btn btn-outline-secondary px-4 py-2' },
            buttonsStyling: false,
        }).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: `${API}/${id}`, type: 'DELETE',
                success: res => {
                    if (res.success) {
                        Toast.fire({ ...swalCfg(), icon: 'success', title: res.message });
                        activeTable.ajax.reload(null, false);
                        loadStats();
                    }
                },
                error: xhr => {
                    Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal menghapus opname.' });
                },
            });
        });
    });

});
</script>
