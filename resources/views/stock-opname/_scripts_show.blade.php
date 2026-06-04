<script>
document.addEventListener('DOMContentLoaded', function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const OPNAME_ID = {{ $stockOpname->id }};
    const IS_OPEN   = {{ $stockOpname->isOpen() ? 'true' : 'false' }};
    const API       = '/api/stock-opname';

    // =========================================================================
    // DataTables — item opname (server-side)
    // =========================================================================
    const columns = [
        { data: 'DT_RowIndex',           name: 'DT_RowIndex',        orderable: false, searchable: false, className: 'text-center' },
        { data: 'item_display',          name: 'item.name' },
        { data: 'uom_display',           name: 'item.unit_of_measure', orderable: false, searchable: false, className: 'text-center' },
        { data: 'batch_display',         name: 'itemStock.batch_number', orderable: false, searchable: false },
        { data: 'system_qty_display',    name: 'system_quantity',    className: 'text-center', searchable: false },
        { data: 'physical_qty_display',  name: 'physical_quantity',  orderable: false, searchable: false, className: 'text-center' },
        { data: 'difference_display',    name: 'difference',         orderable: false, searchable: false, className: 'text-center diff-cell' },
    ];

    const table = $('#opnameItemsTable').DataTable({
        processing:  true,
        serverSide:  true,
        ajax: {
            url: `${API}/${OPNAME_ID}/items`,
            data: d => {
                d.filter_status = $('#filterCountStatus').val();
            },
        },
        columns,
        order: [[1, 'asc']],
        paging: !IS_OPEN,
        scrollY: IS_OPEN ? '60vh' : undefined,
        scrollCollapse: IS_OPEN,
        pageLength: IS_OPEN ? -1 : 50,
        drawCallback: function () {
            if (IS_OPEN) renderInputRows();
        },
    });

    $('#filterCountStatus').on('change', () => table.draw());
    $('#itemSearch').on('keyup', function () { table.search($(this).val()).draw(); });

    // =========================================================================
    // Render input row di atas setiap row DataTables (hanya jika open)
    // =========================================================================
    function renderInputRows() {
        // Karena paging mati, table.rows().every() iterasi seluruh data yg ada di viewport datatables
        table.rows().every(function () {
            const data   = this.data();
            const $row   = $(this.node());
            const $cell  = $row.find('td:eq(5)'); // kolom physical_qty

            // Skip jika sudah ada input
            if ($cell.find('input[type=number]').length) return;

            const opnameItemId = data.opname_item_id ?? (data.DT_RowData?.opname_item_id ?? null);
            if (!opnameItemId) return;

            const systemQty  = data.system_quantity ?? 0;
            // Kembalikan ke default kosong ('') jika belum dihitung di database
            const currentVal = data.physical_quantity ?? '';

            $cell.html(`
                <div class="d-flex align-items-center justify-content-center gap-1">
                    <input type="hidden" name="items[${opnameItemId}][opname_item_id]" value="${opnameItemId}">
                    <input type="number"
                           class="form-control form-control-sm text-center physical-input"
                           name="items[${opnameItemId}][physical_quantity]"
                           data-id="${opnameItemId}"
                           data-system="${systemQty}"
                           value="${currentVal}"
                           min="0"
                           style="width:75px;font-size:13px;margin:0 auto"
                           placeholder="—">
                </div>
            `);
        });

        updateSelectedCount();
    }

    if (IS_OPEN) {
        // Hitung & tampilkan selisih secara realtime saat input berubah
        $(document).on('input', '.physical-input', function () {
            const $row   = $(this).closest('tr');
            const system = parseInt($(this).data('system')) || 0;
            const phys   = $(this).val();
            const $diff  = $row.find('.diff-cell');

            if (phys === '' || phys === null) {
                $diff.html('<span class="text-muted">—</span>');
            } else {
                const diff  = parseInt(phys) - system;
                const sign  = diff > 0 ? '+' : '';
                const color = diff === 0 ? 'success' : (diff > 0 ? 'info' : 'danger');
                $diff.html(`<span class="fw-bold text-${color}">${sign}${diff}</span>`);
            }

            updateSelectedCount();
        });

        function updateSelectedCount() {
            const filled = $('.physical-input').filter(function () {
                return $(this).val() !== '' && $(this).val() !== null;
            }).length;
            const total  = $('.physical-input').length;
            $('#selectedCount').text(`${filled} dari ${total} item sudah diisi`);
            $('#btnSaveItems').prop('disabled', filled === 0);
        }

        // Submit form
        $('#opnameForm').on('submit', function () {
            $('#btnSaveSpinner').removeClass('d-none');
            $('#btnSaveItems').prop('disabled', true);

            // Hapus input kosong agar tidak terkirim (server tidak wajib semua terisi)
            $(this).find('.physical-input').each(function () {
                if ($(this).val() === '') {
                    $(this).prop('disabled', true);
                    $(this).siblings('input[type="hidden"]').prop('disabled', true);
                }
            });
        });
    }

});
</script>
