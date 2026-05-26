@extends('layouts.app')

@section('title', 'Master Barang')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Master Barang</h4>
            <p class="text-muted mb-0" style="font-size: 14px;">Manajemen data induk barang dan stok awal</p>
        </div>
        <div>
            <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                data-bs-target="#itemModal" id="btnAddItem" style="border-radius: var(--radius-md);">
                <i class="bi bi-plus-lg"></i>
                <span>Barang Baru</span>
            </button>
        </div>
    </div>

    <!-- Stats Widgets -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 p-3 h-100" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary"
                        style="width: 48px; height: 48px; border-radius: 50%;">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1" style="font-size: 13px;">Total Barang</p>
                        <h4 class="mb-0 fw-bold" data-count="{{ $totalItems }}">0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
        <div class="card-body p-4">

            <!-- Filter Section -->
            <div class="row mb-4 g-2">
                <div class="col-md-3">
                    <select id="filterCategory" class="form-select" style="border-radius: var(--radius-md);">
                        <option value="all">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filterType" class="form-select" style="border-radius: var(--radius-md);">
                        <option value="all">Semua Tipe</option>
                        <option value="consumable">Habis Pakai</option>
                        <option value="inventory">Inventaris</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filterStatus" class="form-select" style="border-radius: var(--radius-md);">
                        <option value="all">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Non-Aktif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"
                            style="border-radius: var(--radius-md) 0 0 var(--radius-md);">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="searchItem" class="form-control border-start-0"
                            placeholder="Cari nama / kode..." style="border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="itemTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">NO</th>
                            <th style="width: 70px;">FOTO</th>
                            <th>KODE</th>
                            <th>NAMA BARANG</th>
                            <th>KATEGORI</th>
                            <th>TIPE</th>
                            <th>STOK</th>
                            <th>SATUAN</th>
                            <th>STATUS</th>
                            <th class="text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Modal Form Barang -->
    <div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="itemModalLabel">Tambah Barang Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="itemForm" enctype="multipart/form-data">
                    <div class="modal-body pb-0">
                        @csrf
                        <input type="hidden" id="item_id" name="id">
                        <input type="hidden" id="form_method" name="_method" value="POST">

                        <div class="row">
                            <!-- Left Column (Image) -->
                            <div class="col-md-4 text-center mb-4">
                                <div class="mb-2">
                                    <img id="imagePreview" src="{{ asset('images/no-image.svg') }}" alt="Preview"
                                         class="img-thumbnail rounded" style="width: 100%; height: 200px; object-fit: cover;">
                                </div>
                                <input type="file" id="item_image" name="image" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg">
                                <div class="invalid-feedback text-start" id="item_image_error"></div>
                                <small class="text-muted d-block mt-1">Format: JPG, PNG. Max: 2MB.</small>
                            </div>

                            <!-- Right Column (Data) -->
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Kode Barang <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="item_code" name="code"
                                        placeholder="Contoh: ITM-001">
                                    <div class="invalid-feedback" id="item_code_error"></div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-medium">Nama Barang <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="item_name" name="name"
                                        placeholder="Contoh: Kertas HVS A4">
                                    <div class="invalid-feedback" id="item_name_error"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Kategori <span class="text-danger">*</span></label>
                                        <select class="form-select" id="item_category_id" name="category_id">
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach ($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="item_category_id_error"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Tipe <span class="text-danger">*</span></label>
                                        <select class="form-select" id="item_item_type" name="item_type">
                                            <option value="consumable">Habis Pakai</option>
                                            <option value="inventory">Inventaris</option>
                                        </select>
                                        <div class="invalid-feedback" id="item_item_type_error"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Satuan <span class="text-danger">*</span></label>
                                        <select class="form-select" id="item_unit_of_measure" name="unit_of_measure">
                                            <option value="">-- Pilih Satuan --</option>
                                            @foreach ($units as $unit)
                                                <option value="{{ $unit }}">{{ $unit }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="item_unit_of_measure_error"></div>
                                    </div>

                                    <div class="col-md-6 mb-3" id="initialStockContainer">
                                        <label class="form-label fw-medium">Stok Awal</label>
                                        <input type="number" class="form-control" id="item_initial_stock" name="initial_stock"
                                            placeholder="0" min="0" value="0">
                                        <div class="invalid-feedback" id="item_initial_stock_error"></div>
                                        <small class="text-muted">Isi jika ada stok awal.</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-medium">Deskripsi</label>
                                    <textarea class="form-control" id="item_description" name="description" rows="2"
                                        placeholder="Keterangan barang..."></textarea>
                                    <div class="invalid-feedback" id="item_description_error"></div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="item_is_active"
                                            name="is_active" value="1" checked style="cursor: pointer;">
                                        <label class="form-check-label" for="item_is_active"
                                            style="cursor: pointer;">Barang Aktif</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                            style="border-radius: var(--radius-md);">Batal</button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                            id="btnSaveItem" style="border-radius: var(--radius-md);">
                            <span class="spinner-border spinner-border-sm d-none" id="spinnerItem"
                                role="status" aria-hidden="true"></span>
                            <span id="textItem">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal View Stocks -->
    <div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="stockModalLabel">Detail Stok Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="stockTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal Masuk</th>
                                    <th>Kuantitas</th>
                                    <th>Batch / Lot</th>
                                    <th>Expired Date</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="stockTableBody">
                                <!-- Data akan dimuat via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Stok Aman -->
    <div class="modal fade" id="minStockModal" tabindex="-1" aria-labelledby="minStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="minStockModalLabel">Set Stok Aman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="minStockForm">
                    <div class="modal-body pb-0">
                        @csrf
                        <input type="hidden" id="min_stock_item_id">
                        <div class="mb-3">
                            <label class="form-label fw-medium" id="min_stock_item_name_label">Nama Barang</label>
                            <input type="number" class="form-control" id="min_stock_value" name="minimum_stock" min="0" required>
                            <div class="invalid-feedback" id="min_stock_value_error"></div>
                            <small class="text-muted d-block mt-1">Sistem akan memberi peringatan jika stok kurang dari batas ini.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: var(--radius-md);">Batal</button>
                        <button type="submit" class="btn btn-warning d-flex align-items-center gap-2" id="btnSaveMinStock" style="border-radius: var(--radius-md);">
                            <span class="spinner-border spinner-border-sm d-none" id="spinnerMinStock" role="status" aria-hidden="true"></span>
                            <span id="textMinStock">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Animate Counter First Load
            $('[data-count]').each(function() {
                window.animateCountElement($(this)[0], parseInt($(this).attr('data-count')));
            });

            // Initialize DataTable
            let itemTable = $('#itemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('items.index') }}",
                    data: function(d) {
                        d.search = $('#searchItem').val();
                        d.category = $('#filterCategory').val();
                        d.type = $('#filterType').val();
                        d.status = $('#filterStatus').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'code', name: 'code' },
                    { data: 'name', name: 'name' },
                    { data: 'category.name', name: 'category.name', defaultContent: '-' },
                    { data: 'item_type', name: 'item_type' },
                    { data: 'total_stock', name: 'total_stock', className: 'text-center fw-bold text-primary' },
                    { data: 'unit_of_measure', name: 'unit_of_measure' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
                ],
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm');
                }
            });

            // Trigger Search & Filter
            $('#searchItem').on('keyup', function() {
                itemTable.draw();
            });
            $('#filterCategory, #filterType, #filterStatus').on('change', function() {
                itemTable.draw();
            });

            // Handle Image Preview
            $('#item_image').change(function(){
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#imagePreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });

            // Open Modal for Create
            $('#btnAddItem').click(function() {
                $('#itemModalLabel').text('Tambah Barang Baru');
                $('#itemForm')[0].reset();
                $('#item_id').val('');
                $('#form_method').val('POST');
                $('#imagePreview').attr('src', "{{ asset('images/no-image.svg') }}");
                $('#initialStockContainer').show();

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').empty();
            });

            // Open Modal for Edit
            $(document).on('click', '.editItemBtn', function() {
                const id = $(this).data('id');
                $('#itemForm')[0].reset();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').empty();
                $('#initialStockContainer').hide(); // Sembunyikan stok awal saat edit

                $.get(`{{ url('items') }}/${id}/edit`, function(response) {
                    if (response.success) {
                        const item = response.data;
                        $('#itemModalLabel').text('Edit Barang');
                        $('#item_id').val(item.id);
                        $('#form_method').val('PUT');

                        $('#item_code').val(item.code);
                        $('#item_name').val(item.name);
                        $('#item_category_id').val(item.category_id);
                        $('#item_item_type').val(item.item_type);
                        $('#item_unit_of_measure').val(item.unit_of_measure);
                        $('#item_description').val(item.description);
                        $('#item_is_active').prop('checked', item.is_active);

                        if(response.image_url) {
                            $('#imagePreview').attr('src', response.image_url);
                        } else {
                            $('#imagePreview').attr('src', "{{ asset('images/no-image.svg') }}");
                        }

                        $('#itemModal').modal('show');
                    }
                });
            });

            // Save Data
            $('#itemForm').submit(function(e) {
                e.preventDefault();
                const id = $('#item_id').val();
                let url = id ? `{{ url('items') }}/${id}` : `{{ route('items.store') }}`;

                // Gunakan FormData untuk support file upload
                let formData = new FormData(this);

                // UI Loading
                $('#spinnerItem').removeClass('d-none');
                $('#btnSaveItem').prop('disabled', true);
                $('#textItem').text('Menyimpan...');

                $.ajax({
                    url: url,
                    type: 'POST', // Walaupun edit, method di AJAX tetap POST karena FormData, route akan mendeteksi input _method=PUT
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#spinnerItem').addClass('d-none');
                        $('#btnSaveItem').prop('disabled', false);
                        $('#itemModal').modal('hide');

                        itemTable.ajax.reload(null, false);
                        reloadCounterData();

                        Toast.fire({
                            ...getSwalConfig(),
                            icon: 'success',
                            title: response.message
                        });
                    },
                    error: function(xhr) {
                        $('#spinnerItem').addClass('d-none');
                        $('#btnSaveItem').prop('disabled', false);
                        $('#textItem').text('Simpan');

                        $('.invalid-feedback').empty();
                        $('.is-invalid').removeClass('is-invalid');

                        if(xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $('#item_' + key).addClass('is-invalid');
                                $('#item_' + key + '_error').text(value[0]);
                            });
                        } else {
                            Toast.fire({
                                ...getSwalConfig(),
                                icon: 'error',
                                title: 'Gagal menghubungi server!'
                            });
                        }
                    }
                });
            });

            // Delete Data
            $(document).on('click', '.deleteItemBtn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                Swal.fire({
                    title: 'Hapus Barang?',
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
                            url: `{{ url('items') }}/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    itemTable.ajax.reload();
                                    reloadCounterData();
                                    Swal.fire('Terhapus!', response.message, 'success');
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message,
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                }
                            }
                        });
                    }
                });
            });

            // Toggle Status
            $(document).on('change', '.btn-toggle-status', function() {
                const id = $(this).data('id');
                const checkbox = $(this);

                $.ajax({
                    url: `/items/${id}/toggle`,
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
                        checkbox.prop('checked', !checkbox.prop('checked'));
                        Swal.fire({
                            ...getSwalConfig(),
                            icon: 'error',
                            title: 'Gagal Mengubah Status!'
                        });
                    }
                });
            });

            // View Stocks Modal
            $(document).on('click', '.viewStockBtn', function() {
                const id = $(this).data('id');

                $.get(`{{ url('items') }}/${id}/stocks`, function(response) {
                    if(response.success) {
                        let html = '';
                        if(response.data.length === 0) {
                            html = '<tr><td colspan="5" class="text-center text-muted">Belum ada stok barang.</td></tr>';
                        } else {
                            response.data.forEach(function(stock) {
                                // Format tanggal
                                let dateObj = new Date(stock.created_at);
                                let formattedDate = dateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

                                html += `<tr>
                                    <td>${formattedDate}</td>
                                    <td class="fw-bold">${stock.quantity}</td>
                                    <td>${stock.batch_number || '-'}</td>
                                    <td>${stock.expired_date || '-'}</td>
                                    <td>${stock.notes || '-'}</td>
                                </tr>`;
                            });
                        }
                        $('#stockTableBody').html(html);
                        $('#stockModal').modal('show');
                    }
                });
            });

            // Minimum Stock Modal
            $(document).on('click', '.minStockBtn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const minStock = $(this).data('min');

                $('#min_stock_item_id').val(id);
                $('#min_stock_item_name_label').text(`Stok Aman: ${name}`);
                $('#min_stock_value').val(minStock);

                $('#min_stock_value').removeClass('is-invalid');
                $('#min_stock_value_error').empty();

                $('#minStockModal').modal('show');
            });

            // Save Minimum Stock
            $('#minStockForm').submit(function(e) {
                e.preventDefault();
                const id = $('#min_stock_item_id').val();

                $('#spinnerMinStock').removeClass('d-none');
                $('#btnSaveMinStock').prop('disabled', true);
                $('#textMinStock').text('Menyimpan...');

                $.ajax({
                    url: `/items/${id}/minimum-stock`,
                    type: 'PATCH',
                    data: {
                        minimum_stock: $('#min_stock_value').val()
                    },
                    success: function(response) {
                        $('#spinnerMinStock').addClass('d-none');
                        $('#btnSaveMinStock').prop('disabled', false);
                        $('#minStockModal').modal('hide');

                        $('#textMinStock').text('Simpan');
                        $('#min_stock_value').val('');

                        itemTable.ajax.reload(null, false);

                        Toast.fire({
                            ...getSwalConfig(),
                            icon: 'success',
                            title: response.message
                        });
                    },
                    error: function(xhr) {
                        $('#spinnerMinStock').addClass('d-none');
                        $('#btnSaveMinStock').prop('disabled', false);
                        $('#textMinStock').text('Simpan');

                        if(xhr.responseJSON && xhr.responseJSON.errors) {
                            $('#min_stock_value').addClass('is-invalid');
                            $('#min_stock_value_error').text(xhr.responseJSON.errors.minimum_stock[0]);
                        } else {
                            Toast.fire({
                                ...getSwalConfig(),
                                icon: 'error',
                                title: xhr.responseJSON.message || 'Gagal menyimpan stok aman.'
                            });
                        }
                    }
                });
            });

            // Helper to reload counter data
            function reloadCounterData() {
                $.ajax({
                    url: "{{ route('items.index') }}",
                    type: 'GET',
                    dataType: 'html',
                    success: function(htmlResponse) {
                        const parsedHtml = $($.parseHTML(htmlResponse));

                        $('[data-count]').each(function() {
                            const countName = $(this).closest('.card').find('.text-muted').text().trim();
                            let targetVal = 0;

                            parsedHtml.find('[data-count]').each(function() {
                                const parsedName = $(this).closest('.card').find('.text-muted').text().trim();
                                if (countName === parsedName) {
                                    targetVal = $(this).attr('data-count');
                                    return false;
                                }
                            });

                            $(this).attr('data-count', targetVal);
                            window.animateCountElement($(this)[0], parseInt(targetVal));
                        });
                    }
                });
            }
        });
    </script>
@endpush
