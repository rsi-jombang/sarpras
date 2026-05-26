@extends('layouts.app')

@section('title', 'Master Kategori')

@section('content')

    <div class="container-fluid px-4 py-4">
        <!-- Header Halaman -->
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
            <div>
                <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary)">Master Kategori</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="font-size: 12.5px">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-decoration-none"
                                style="color: var(--text-muted)">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="#" class="text-decoration-none" style="color: var(--text-muted)">Data Master</a>
                        </li>
                        <li class="breadcrumb-item text-primary fw-bold" aria-current="page">Master Kategori</li>
                    </ol>
                </nav>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2" id="btnCreateCategory"
                    style="border-radius: var(--radius-md); background: var(--accent-primary); border-color: var(--accent-primary)">
                    <i class="bi bi-plus-lg"></i> Tambah Kategori
                </button>
            </div>

        </div>

        <!-- Kartu Statistik -->
        <div class="row g-3 mb-4 animate-in">
            <div class="col-12 col-md-4">
                <div class="card border-0 px-3 py-3"
                    style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-subtle)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon-wrap"
                            style="background: rgba(79, 70, 229, 0.1); color: var(--accent-primary)">
                            <i class="bi bi-hospital"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 12.5px; font-weight: 500">Total
                                Kategori</span>
                            <h3 class="fw-bold mb-0 mt-0.5" data-count="{{ $totalCategories }}"
                                style="color: var(--text-primary)">0</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 px-3 py-3"
                    style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-subtle)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon-wrap" style="background: rgba(34, 197, 94, 0.1); color: #22c55e">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 12.5px; font-weight: 500">Kategori Habis
                                Pakai</span>
                            <h3 class="fw-bold mb-0 mt-0.5" data-count="{{ $consumableCategory }}"
                                style="color: var(--text-primary)">0</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 px-3 py-3"
                    style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-subtle)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon-wrap" style="background: rgba(239, 68, 68, 0.1); color: #ef4444">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 12.5px; font-weight: 500">Kategori
                                Inventaris</span>
                            <h3 class="fw-bold mb-0 mt-0.5" data-count="{{ $inventoryCategory }}"
                                style="color: var(--text-primary)">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card animate-in"
            style="background: var(--bg-surface); border-color: var(--border-color); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 gap-3">

                    <div class="position-relative" style="max-width: 400px;">

                        <input id="searchInput" type="text" class="form-control ps-5 py-2" placeholder="Cari kategori..."
                            style="border-radius: var(--radius-md); background: var(--bg-surface-2); border-color: var(--border-color); color: var(--text-primary);">

                        <div id="clearSearch" class="position-absolute top-50 end-0 translate-middle-y d-none me-3"
                            style="cursor: pointer; color: var(--text-muted);">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                    </div>

                    <!-- Dropdown filter -->
                    <div class="d-flex gap-2">
                        <select id="filterType" class="form-select" style="border-radius: var(--radius-md);">
                            <option value="all">Semua Tipe Barang</option>
                            <option value="consumable">Consumable</option>
                            <option value="inventory">Inventory</option>
                        </select>

                        <select id="filterStatus" class="form-select" style="border-radius: var(--radius-md);">
                            <option value="all">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="categoryTable" class="table table-borderless table-hover w-100" style="font-size: 13px;">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start text-uppercase text-muted fw-bold" style="width: 50px;">No</th>
                                <th class="text-start text-uppercase text-muted fw-bold">Nama Kategori</th>
                                <th class="text-start text-uppercase text-muted fw-bold">Kode</th>
                                <th class="text-start text-uppercase text-muted fw-bold">Tipe Barang</th>
                                <th class="text-start text-uppercase text-muted fw-bold" style="width: 100px;">Status</th>
                                <th class="text-end text-uppercase text-muted fw-bold" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- MODAL TAMBAH/EDIT KATEGORI -->
        <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="categoryModalTitle">Tambah Kategori Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form id="categoryForm">
                        @csrf
                        <input type="hidden" name="id" id="category_id">

                        <div class="modal-body">

                            <div class="mb-3">
                                <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="category_name" class="form-control" required>
                                <div class="invalid-feedback" id="category_name_error"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kode Kategori <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="category_code" class="form-control" required>
                                <div class="invalid-feedback" id="category_code_error"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tipe Barang <span class="text-danger">*</span></label>
                                <select name="item_type" id="category_item_type" class="form-select">
                                    <option value="consumable">Barang Habis Pakai</option>
                                    <option value="inventory">Barang Inventaris</option>
                                </select>
                                <div class="invalid-feedback" id="category_item_type_error"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" id="category_description" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="is_active" id="category_is_active" class="form-select">
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                style="border-radius: var(--radius-md)">Batal</button>
                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                id="btnSaveCategory"
                                style="border-radius: var(--radius-md); background: var(--accent-primary); border-color: var(--accent-primary)">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"
                                    id="spinnerCategory"></span>
                                <span id="textCategory">Simpan</span>
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // CSRF Token Setup untuk AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // 1. INIT DATATABLE
            // -------------------------------------------
            const categoryTable = $('#categoryTable').DataTable({
                processing: true,
                serverSide: true,
                stateSave: true,
                ajax: {
                    url: "{{ route('category.index') }}",
                    type: "GET",
                    data: function(d) {
                        d.search = $('#searchInput').val();
                        d.type = $('#filterType').val();
                        d.status = $('#filterStatus').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'code'
                    },
                    {
                        data: 'item_type'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-end'
                    }
                ],
                createdRow: function(row, data, dataIndex) {
                    // Warnai row berdasarkan status
                    if (data.is_active == 0) {
                        $(row).css('background-color', 'rgba(255,0,0,0.03)');
                        $(row).find('td').css('opacity', '0.7');
                    }
                }
            });

            // ---------------------------------------------
            // FILTER & SEARCH LOGIC
            // -------------------------------------------
            $('#filterType, #filterStatus').on('change', function() {
                categoryTable.ajax.reload();
            });

            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                const val = $(this).val();

                // Show/hide clear button
                if (val.length > 0) {
                    $('#clearSearch').removeClass('d-none');
                } else {
                    $('#clearSearch').addClass('d-none');
                }

                // Debounce search so it doesn't spam the server on every keystroke
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    categoryTable.ajax.reload();
                }, 500);
            });

            $('#clearSearch').on('click', function() {
                $('#searchInput').val('');
                $(this).addClass('d-none');
                categoryTable.ajax.reload();
            });

            // -------------------------------------------
            // TAMBAH KATEGORI
            // -------------------------------------------
            $('#btnCreateCategory').click(function() {
                $('#categoryModalTitle').text('Tambah Kategori Baru');
                $('#categoryForm')[0].reset();
                $('#categoryForm').attr('action', "{{ route('category.store') }}");
                $('#categoryForm').find('input[name="id"]').val('');
                $('#categoryForm').find('.invalid-feedback').empty();
                $('.is-invalid').removeClass('is-invalid');
                $('#categoryModal').modal('show');
            });

            // -------------------------------------------
            // EDIT KATEGORI
            // -------------------------------------------
            $(document).on('click', '.editCategoryBtn', function() {
                const categoryId = $(this).data('id');
                const modal = $('#categoryModal');

                // Load data dengan AJAX
                $.get(`{{ url('category') }}/${categoryId}`, function(response) {
                    const data = response.data;
                    $('#categoryModalTitle').text('Edit Kategori');
                    $('#categoryForm').attr('action', `{{ url('category') }}/${categoryId}`);
                    $('#categoryForm').find('input[name="id"]').val(data.id);
                    $('#categoryForm').find('input[name="name"]').val(data.name);
                    $('#categoryForm').find('input[name="code"]').val(data.code);
                    $('#categoryForm').find('select[name="item_type"]').val(data.item_type);
                    $('#categoryForm').find('textarea[name="description"]').val(data.description);
                    $('#categoryForm').find('select[name="is_active"]').val(data.is_active ? '1' :
                        '0');
                    $('#categoryForm').find('.invalid-feedback').empty();
                    $('.is-invalid').removeClass('is-invalid');
                    modal.modal('show');
                });
            });

            // -------------------------------------------
            // SIMPAN KATEGORI (AJAX)
            // -------------------------------------------
            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();

                const actionUrl = $(this).attr('action');

                // Bersihkan validasi error yang lama
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                // Loading state
                $('#spinnerCategory').removeClass('d-none');
                $('#btnSaveCategory').prop('disabled', true);
                $('#textCategory').text('Menyimpan...');

                const categoryId = $('#categoryForm').find('input[name="id"]').val();
                const isEdit = categoryId !== '';
                const url = isEdit ? `/category/${categoryId}` : '/category';
                const method = isEdit ? 'PUT' : 'POST';

                const formData = $(this).serializeArray();

                // Handle checkbox value (since serializeArray won't include unchecked boxes)
                const isActiveIndex = formData.findIndex(item => item.name === 'is_active');
                if (isActiveIndex === -1) {
                    formData.push({
                        name: 'is_active',
                        value: '0'
                    });
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    dataType: 'JSON',
                    success: function(response) {
                        // Loading state
                        $('#spinnerCategory').addClass('d-none');
                        $('#btnSaveCategory').prop('disabled', false);

                        $('#categoryModal').modal('hide');
                        updateStatsWidget();

                        categoryTable.ajax.reload(null, false);

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data kategori berhasil disimpan!',
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    },
                    error: function(xhr) {
                        // Loading state
                        $('#spinnerCategory').addClass('d-none');
                        $('#btnSaveCategory').prop('disabled', false);
                        $('#textCategory').text('Simpan');

                        // Tampilkan error
                        $('.invalid-feedback').empty();
                        $('.is-invalid').removeClass('is-invalid');

                        $.each(xhr.responseJSON.errors, function(key, value) {
                            $('#category_' + key).addClass('is-invalid');
                            $('#category_' + key + '_error').text(value[0]);
                        });
                    }
                });
            });

            // -------------------------------------------
            // DELETE KATEGORI
            // -------------------------------------------
            $(document).on('click', '.deleteCategoryBtn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                Swal.fire({
                    title: 'Hapus Kategori?',
                    text: `Apakah anda yakin ingin menghapus "${name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74c3c',
                    cancelButtonColor: '#34495e',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('category') }}/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    categoryTable.ajax.reload();
                                    reloadCounterData();
                                    Swal.fire(
                                        'Terhapus!',
                                        response.message,
                                        'success'
                                    );
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message,
                                        timer: 3000,
                                        showConfirmButton: false,
                                        toast: true,
                                        position: 'top-end'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: 'Gagal menghubungi server.',
                                    timer: 2000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            }
                        });
                    }
                });
            });

            // -------------------------------------------
            // TOGGLE STATUS
            // -------------------------------------------
            // 8. Toggle Status Switcher Langsung di Tabel
            $(document).on('change', '.btn-toggle-status', function() {
                const id = $(this).data('id');
                const checkbox = $(this);

                $.ajax({
                    url: `/category/${id}/toggle`,
                    type: 'PATCH',
                    success: function(response) {
                        if (response.success) {
                            Toast.fire({
                                ...getSwalConfig(),
                                icon: 'success',
                                title: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        // Revert status switcher jika gagal
                        checkbox.prop('checked', !checkbox.prop('checked'));

                        Swal.fire({
                            ...getSwalConfig(),
                            icon: 'error',
                            title: 'Gagal Mengubah Status!',
                            text: xhr.responseJSON.message ||
                                'Terjadi kesalahan sistem internal.'
                        });
                    }
                });
            });

            // Helper untuk mengupdate Counter Widgets secara client-side pasca AJAX CRUD
            function updateStatsWidget(isEdit, formData) {
                // Untuk kesempurnaan performa, kita muat ulang data statistik secara dinamis dari database
                reloadCounterData();
            }

            // Fetch live counts from database to refresh widgets dynamically without full reload
            function reloadCounterData() {
                $.ajax({
                    url: "{{ route('category.index') }}",
                    type: 'GET',
                    dataType: 'html', // Panggil halaman biasa
                    success: function(htmlResponse) {
                        // Parsing respons HTML secara aman menggunakan $.parseHTML
                        const parsedHtml = $($.parseHTML(htmlResponse));

                        $('[data-count]').each(function() {
                            const countName = $(this).closest('.card').find('.text-muted')
                            .text().trim();
                            let targetVal = 0;

                            parsedHtml.find('[data-count]').each(function() {
                                const parsedName = $(this).closest('.card').find(
                                    '.text-muted').text().trim();
                                if (countName === parsedName) {
                                    targetVal = $(this).attr('data-count');
                                    return false; // Break
                                }
                            });

                            // Update data-count attribute
                            $(this).attr('data-count', targetVal);

                            // Re-animate the count number
                            window.animateCountElement($(this)[0], parseInt(targetVal));
                        });
                    }
                });
            }
        });
    </script>
@endpush
