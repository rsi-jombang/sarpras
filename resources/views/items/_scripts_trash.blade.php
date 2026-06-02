<script>
document.addEventListener('DOMContentLoaded', function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const API    = '/api/items';
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
    // DataTables — Trash
    // =========================================================================
    const table = $('#trashTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: `${API}/trash`,
            dataSrc: function (json) {
                $('#trashCount').text(json.recordsTotal ?? 0);
                return json.data;
            },
        },
        columns: [
            { data: 'DT_RowIndex',        name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'name_display',       name: 'name' },
            { data: 'category_display',   name: 'category_id', orderable: false },
            { data: 'type_badge',         name: 'item_type',   orderable: false, className: 'text-center' },
            { data: 'deleted_at_display', name: 'deleted_at' },
            { data: 'action',             name: 'action',      orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[4, 'desc']],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
    });

    $('#trashSearch').on('keyup', function () { table.search($(this).val()).draw(); });

    // =========================================================================
    // Restore
    // =========================================================================
    $(document).on('click', '.btn-restore', function () {
        const id = $(this).data('id');
        Swal.fire({
            ...swalCfg(),
            title: 'Pulihkan Barang?',
            text: 'Barang akan dikembalikan ke daftar aktif.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-arrow-counterclockwise"></i> Pulihkan',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: 'btn btn-success px-4 py-2 me-2', cancelButton: 'btn btn-outline-secondary px-4 py-2' },
            buttonsStyling: false,
        }).then(result => {
            if (!result.isConfirmed) return;
            $.post(`${API}/${id}/restore`, {}, res => {
                if (res.success) {
                    Toast.fire({ ...swalCfg(), icon: 'success', title: res.message });
                    table.ajax.reload(null, false);
                }
            }).fail(xhr => {
                Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal memulihkan barang.' });
            });
        });
    });

    // =========================================================================
    // Force Delete
    // =========================================================================
    $(document).on('click', '.btn-force-delete', function () {
        const id   = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            ...swalCfg(),
            title: 'Hapus Permanen?',
            html: `Barang <strong>${name}</strong> akan dihapus selamanya.<br>
                   <small class="text-danger">Tindakan ini tidak dapat dibatalkan.</small>`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-trash3"></i> Hapus Permanen',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: 'btn btn-danger px-4 py-2 me-2', cancelButton: 'btn btn-outline-secondary px-4 py-2' },
            buttonsStyling: false,
        }).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: `${API}/${id}/force-delete`, type: 'DELETE',
                success: res => {
                    if (res.success) {
                        Toast.fire({ ...swalCfg(), icon: 'success', title: res.message });
                        table.ajax.reload(null, false);
                    }
                },
                error: xhr => {
                    Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal menghapus permanen.' });
                },
            });
        });
    });

});
</script>
