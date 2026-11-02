<script>
document.addEventListener('DOMContentLoaded', function() {
    const API = '/api/requests';
    let itemIndex = 0;
    let itemsMaster = [];

    $.get(API + '/items', res => {
        if (res.success) itemsMaster = res.data;
        $('.item-select').each(function() {
            const v = $(this).val() || $(this).data('selected-id');
            $(this).html(buildOptions(v));
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({ theme: 'bootstrap-5', placeholder: 'Cari barang', width: '100%', dropdownParent: $(this).closest('td') });
            }
        });
    });

    function buildOptions(selectedId = null) {
        let html = '<option value="">-- Pilih Barang --</option>';
        itemsMaster.forEach(i => {
            const sel = selectedId && String(i.id) === String(selectedId) ? 'selected' : '';
            html += `<option value="${i.id}" data-uom="${i.unit_of_measure}" data-stock="${i.total_stock}" ${sel}>${i.name} (${i.code})</option>`;
        });
        return html;
    }

    function addRow(data = {}) {
        const tpl = document.getElementById('itemRowTpl').innerHTML.replaceAll('__INDEX__', itemIndex);
        const $row = $(tpl);
        const $select = $row.find('.item-select');
        if (data.item_id) $select.attr('data-selected-id', data.item_id);
        $select.html(buildOptions(data.item_id));
        if (data.item_id && itemsMaster.length) {
            const found = itemsMaster.find(i => String(i.id) === String(data.item_id));
            if (found) { $row.find('.item-uom').text(found.unit_of_measure); $row.find('.item-stock').text(found.total_stock); }
        }
        if (data.quantity_returned) $row.find('.item-qty').val(data.quantity_returned);
        $('#itemsBody').append($row);
        if (itemsMaster.length) {
            $select.select2({ theme: 'bootstrap-5', placeholder: 'Cari barang', width: '100%', dropdownParent: $select.closest('td') });
        }
        itemIndex++;
        $('#emptyMsg').toggle($('#itemsBody .item-row').length === 0);
    }

    $('#btnAddItem').on('click', () => addRow());
    $('#itemsBody').on('change', '.item-select', function() {
        const opt = $(this).find(':selected');
        const row = $(this).closest('.item-row');
        row.find('.item-uom').text(opt.data('uom') || '—');
        row.find('.item-stock').text(opt.val() ? (opt.data('stock') ?? 0) : '—');
    }).on('click', '.btn-remove', function() {
        $(this).closest('.item-row').remove();
        $('#emptyMsg').toggle($('#itemsBody .item-row').length === 0);
    });

    if (window.__EXISTING_ITEMS__ && window.__EXISTING_ITEMS__.length) {
        window.__EXISTING_ITEMS__.forEach(i => addRow({
            item_id: i.item_id,
            quantity_requested: i.quantity_returned,
            notes: i.notes || '',
        }));
        $('#emptyMsg').hide();
    } else {
        addRow();
    }
    $('#emptyMsg').toggle($('#itemsBody .item-row').length === 0);
});
</script>