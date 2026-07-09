<script>
document.addEventListener('DOMContentLoaded', function () {

    const API = '/api/requests';
    let itemIndex = $('.item-row').length;
    let itemsMaster = [];

    // Load daftar barang dari API
    $.get(`${API}/items`, res => {
        if (res.success) {
            itemsMaster = res.data;
            // Update the options for any rows added before items were loaded
            $('.item-select').each(function() {
                const currentVal = $(this).val() || $(this).data('selected-id');
                $(this).html(buildOptions(currentVal));
                $(this).select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Cari barang',
                    width: '100%',
                    dropdownParent: $(this).closest('td')
                });
            });
        }
    });

    function buildOptions(selectedId = null) {
        let html = '<option value="">-- Pilih Barang --</option>';
        itemsMaster.forEach(item => {
            const sel = selectedId && String(item.id) === String(selectedId) ? 'selected' : '';
            html += `<option value="${item.id}"
                data-uom="${item.unit_of_measure}"
                data-stock="${item.total_stock}"
                ${sel}>${item.name} (${item.code})</option>`;
        });
        return html;
    }

    function toggleEmptyMsg() {
        const empty = $('#itemsBody .item-row').length === 0;
        $('#emptyMsg').toggle(empty);
    }

    function addRow(data = {}) {
        const tpl  = document.getElementById('itemRowTpl').innerHTML.replaceAll('__INDEX__', itemIndex);
        const $row = $(tpl);
        const $select = $row.find('.item-select');

        // Store selected id to use after API loads
        if (data.item_id) {
            $select.attr('data-selected-id', data.item_id);
        }

        $select.html(buildOptions(data.item_id));

        if (data.item_id && itemsMaster.length > 0) {
            const found = itemsMaster.find(i => String(i.id) === String(data.item_id));
            if (found) {
                $row.find('.item-uom').text(found.unit_of_measure);
                const stockColor = found.total_stock === 0 ? 'danger' : (found.total_stock < 5 ? 'warning' : 'success');
                $row.find('.item-stock').text(found.total_stock).attr('class', `item-stock fw-semibold text-${stockColor}`);
            }
        }

        if (data.quantity_requested) $row.find('.item-qty').val(data.quantity_requested);
        if (data.notes) $row.find('.item-note').val(data.notes);

        $('#itemsBody').append($row);

        if (itemsMaster.length > 0) {
            $select.select2({
                theme: 'bootstrap-5',
                placeholder: 'Cari barang',
                width: '100%',
                dropdownParent: $select.closest('td')
            });
        }

        itemIndex++;
        toggleEmptyMsg();
    }

    $('#btnAddItem').on('click', () => addRow());

    // Delegasi event
    $('#itemsBody')
        .on('change', '.item-select', function () {
            const opt   = $(this).find(':selected');
            const $row  = $(this).closest('.item-row');
            const stock = parseInt(opt.data('stock') ?? 0);
            const color = stock === 0 ? 'danger' : (stock < 5 ? 'warning' : 'success');
            $row.find('.item-uom').text(opt.data('uom') || '—');
            $row.find('.item-stock').text(opt.val() ? stock : '—')
                .attr('class', `item-stock fw-semibold text-${color}`);

            // Batasi max qty dengan stok tersedia
            $row.find('.item-qty').attr('max', stock > 0 ? stock : 9999);

            // Validasi jika jumlah lebih besar dari stok
            const $qtyInput = $row.find('.item-qty');
            if (parseFloat($qtyInput.val()) > stock) {
                $qtyInput.val(stock > 0 ? stock : '');
            }
        })
        .on('click', '.btn-remove-item', function () {
            const $select = $(this).closest('.item-row').find('.item-select');
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            $(this).closest('.item-row').remove();
            toggleEmptyMsg();
        });

    // Update max quantity saat input jumlah berubah
    $(document).on('input', '.item-qty', function() {
        const max = parseFloat($(this).attr('max'));
        const val = parseFloat($(this).val());
        if (max && val > max) {
            $(this).val(max);
        }
    });

    // Restore old values jika ada validation error
    @if(old('items'))
        @foreach(old('items', []) as $i => $oldItem)
            addRow({
                item_id:           {{ $oldItem['item_id'] ?? 'null' }},
                quantity_requested: {{ $oldItem['quantity_requested'] ?? 1 }},
                notes:             '{{ addslashes($oldItem['notes'] ?? '') }}',
            });
        @endforeach
    @elseif(!IS_EDIT_MODE)
        addRow(); // minimal 1 baris kosong (hanya untuk create baru)
    @endif

    toggleEmptyMsg();

    // Submit guard
    $('#requestForm').on('submit', function () {
        if ($('#itemsBody .item-row').length === 0) {
            alert('Minimal satu barang harus ditambahkan.');
            return false;
        }
        $('#btnSubmitSpinner').removeClass('d-none');
        $('#btnSubmit').prop('disabled', true);
    });

});
</script>
