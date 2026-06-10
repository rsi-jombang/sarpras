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
    // 1. DATATABLES
    // =========================================================================
    const table = $('#itemTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: API,
            data: d => {
                d.category_id  = $('#filterCategory').val();
                d.item_type    = $('#filterType').val();
                d.stock_status = $('#filterStock').val();
            },
        },
        columns: [
            { data: 'DT_RowIndex',      name: 'DT_RowIndex',    orderable: false, searchable: false, className: 'text-center' },
            { data: 'name_display',     name: 'name' },
            { data: 'category_display', name: 'category_id',   orderable: false },
            { data: 'unit_of_measure',  name: 'unit_of_measure', className: 'text-center' },
            { data: 'stock_display',    name: 'total_stock',    orderable: false, className: 'text-center' },
            { data: 'action',           name: 'action',         orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[1, 'asc']],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
    });

    $('#filterCategory, #filterType, #filterStock').on('change', () => table.draw());
    $('#tableSearch').on('keyup', function () { table.search($(this).val()).draw(); });

    // =========================================================================
    // 2. STATS & CATEGORIES
    // =========================================================================
    let categoriesMaster = [];

    function loadStats() {
        $.get(`${API}/stats`, res => {
            if (!res.success) return;
            ['total','consumable','inventory','low_stock','empty_stock'].forEach(key => {
                const el = document.getElementById(`stat-${key.replace('_','-')}`);
                if (el) el.textContent = res.data[key] ?? 0;
            });
        });
    }

    function loadCategories() {
        $.get(`${API}/categories`, res => {
            if (!res.success) return;
            categoriesMaster = res.data;

            // Isi filter dropdown
            let opts = '<option value="">Semua Kategori</option>';
            res.data.forEach(c => opts += `<option value="${c.id}">${c.name}</option>`);
            $('#filterCategory').html(opts);

            // Isi form select
            let formOpts = '<option value="">-- Pilih Kategori --</option>';
            res.data.forEach(c => formOpts += `<option value="${c.id}" data-type="${c.item_type}">${c.name}</option>`);
            $('#itemCategory').html(formOpts);
        });
    }

    loadStats();
    loadCategories();

    // Auto-set item_type saat kategori dipilih
    $('#itemCategory').on('change', function () {
        const opt = $(this).find(':selected');
        if (opt.data('type')) $('#itemType').val(opt.data('type'));
    });

    // =========================================================================
    // 3. PREVIEW GAMBAR
    // =========================================================================
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

    // =========================================================================
    // 4. FORM MODAL — TAMBAH / EDIT
    // =========================================================================
    function clearFormErrors() {
        $('.form-control,.form-select').removeClass('is-invalid');
        $('[id^="error-"]').text('');
    }

    function showFormErrors(errors) {
        Object.entries(errors).forEach(([key, msgs]) => {
            $(`[name="${key}"]`).addClass('is-invalid');
            $(`#error-${key}`).text(msgs[0]);
        });
    }

    function resetSubmitBtn() {
        $('#btnItemSpinner').addClass('d-none');
        $('#btnItemIcon').removeClass('d-none');
        $('#btnSaveItem').prop('disabled', false);
    }

    // Buka modal tambah
    $('#btnCreateItem').on('click', function () {
        $('#itemId').val('');
        $('#itemForm')[0].reset();
        $('#imagePreviewWrap').hide();
        clearFormErrors();
        $('#itemModalTitle').text('Tambah Barang Baru');
        $('#btnItemText').text('Simpan');
        $('#initialStock').val(0);
        $('#initialStockContainer').show();
        loadCategories();
        $('#itemModal').modal('show');
    });

    // Buka modal edit
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        $.get(`${API}/${id}`, res => {
            if (!res.success) return;
            const item = res.data;
            $('#itemId').val(item.id);
            $('#itemName').val(item.name);
            $('#itemCode').val(item.code);
            $('#itemUom').val(item.unit_of_measure);
            $('#itemType').val(item.item_type);
            $('#itemDesc').val(item.description);
            $('#itemActive').prop('checked', item.is_active);

            // Isi kategori
            loadCategories();
            setTimeout(() => {
                $('#itemCategory').val(item.category_id);
            }, 300);

            // Gambar existing
            if (item.image_path) {
                $('#imagePreview').attr('src', `/storage/${item.image_path}`);
                $('#imagePreviewWrap').show();
            } else {
                $('#imagePreviewWrap').hide();
            }

            clearFormErrors();
            $('#itemModalTitle').text('Edit Barang');
            $('#btnItemText').text('Perbarui');
            $('#initialStockContainer').hide();
            $('#itemModal').modal('show');
        });
    });

    // Submit form — gunakan FormData karena ada file upload
    $('#itemForm').on('submit', function (e) {
        e.preventDefault();
        clearFormErrors();

        const id     = $('#itemId').val();
        const isEdit = !!id;
        const url    = isEdit ? `${API}/${id}` : API;

        const formData = new FormData(this);
        if (isEdit) formData.append('_method', 'POST'); // pakai POST untuk update karena ada file

        $('#btnItemSpinner').removeClass('d-none');
        $('#btnItemIcon').addClass('d-none');
        $('#btnSaveItem').prop('disabled', true);

        $.ajax({
            url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: res => {
                resetSubmitBtn();
                if (res.success) {
                    $('#itemModal').modal('hide');
                    Toast.fire({ ...swalCfg(), icon: 'success', title: res.message });
                    table.ajax.reload(null, false);
                    loadStats();
                }
            },
            error: xhr => {
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
    // 5. DELETE (SOFT)
    // =========================================================================
    $(document).on('click', '.btn-delete', function () {
        const id   = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            ...swalCfg(),
            title: 'Hapus Barang?',
            html: `Barang <strong>${name}</strong> akan dipindahkan ke sampah.<br>
                   <small class="text-muted">Data masih bisa dipulihkan dari halaman Sampah.</small>`,
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
                        table.ajax.reload(null, false);
                        loadStats();
                    }
                },
                error: xhr => {
                    Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal menghapus barang.' });
                },
            });
        });
    });

});
</script>
