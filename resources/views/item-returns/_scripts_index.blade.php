<script>
document.addEventListener('DOMContentLoaded', function () {
    const API = '/api/item-returns';
    const table = $('#returnTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: API,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false, className:'text-center' },
            { data: 'return_number_display', name:'return_number' },
            { data: 'unit_display', name:'unit_id' },
            { data: 'creator_display', name:'created_by' },
            { data: 'status_badge', name:'status', orderable:false, className:'text-center' },
            { data: 'total_qty', name:'total_qty', orderable:false, searchable:false, className:'text-center' },
            { data: 'created_at', name:'created_at', render: function(d){ return new Date(d).toLocaleDateString('id-ID'); } },
            { data: 'action', name:'action', orderable:false, searchable:false, className:'text-end' },
        ],
    });

    // Batalkan
    $(document).on('click', '.btn-cancel', function () {
        const id = $(this).data('id');
        if (!confirm('Batalkan pengembalian ini?')) return;
        $.post(`${API}/${id}/cancel`, {}, function (res) {
            if (res.success) { table.ajax.reload(); }
        });
    });
    // Kirim dari detail via modal? handled on show page
});
</script>