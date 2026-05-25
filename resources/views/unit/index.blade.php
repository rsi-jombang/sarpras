@extends('layouts.app')

@section('title', 'Master Unit')

@push('styles')
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <style>
        /* Override DataTables premium styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button.active .page-link {
            background-color: var(--accent-primary) !important;
            border-color: var(--accent-primary) !important;
            color: #fff !important;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button .page-link {
            color: var(--text-secondary);
            background-color: var(--bg-surface);
            border-color: var(--border-color);
            border-radius: var(--radius-sm);
            padding: 6px 12px;
            transition: var(--transition);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button .page-link:hover {
            background-color: var(--bg-surface-2);
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        .dataTables_wrapper .dataTables_info {
            color: var(--text-secondary) !important;
            font-size: 13px;
        }

        .dataTables_wrapper .dataTables_length select {
            background-color: var(--bg-surface-2) !important;
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
            border-radius: var(--radius-sm) !important;
            font-size: 13px;
            padding: 4px 8px;
        }

        table.dataTable {
            border-color: var(--border-color) !important;
            margin-top: 15px !important;
            margin-bottom: 15px !important;
        }

        table.dataTable thead tr {
            border-bottom: 2px solid var(--border-color) !important;
            background-color: var(--bg-surface-2) !important;
        }

        table.dataTable thead th {
            color: var(--text-secondary) !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px !important;
            border-bottom: none !important;
        }

        table.dataTable tbody td {
            color: var(--text-primary);
            font-size: 13.5px;
            padding: 14px 16px !important;
            border-bottom: 1px solid var(--border-color) !important;
        }

        /* Hide default search box to use our custom premium search input */
        .dataTables_filter {
            display: none;
        }

        /* Switch toggle premium style */
        .form-switch .form-check-input {
            width: 2.2em;
            height: 1.2em;
            background-color: var(--border-color);
            border-color: var(--border-color);
            transition: background-position .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .form-switch .form-check-input:checked {
            background-color: var(--accent-primary);
            border-color: var(--accent-primary);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
        }

        .form-switch .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        /* Premium Modal Overrides */
        .modal-content {
            background: var(--bg-surface) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: var(--radius-lg) !important;
            box-shadow: var(--shadow-lg) !important;
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color) !important;
            padding: 20px 24px !important;
        }

        .modal-footer {
            border-top: 1px solid var(--border-color) !important;
            padding: 16px 24px !important;
        }

        .modal-body {
            padding: 24px !important;
        }

        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .form-control {
            background: var(--bg-surface-2) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: var(--radius-md) !important;
            color: var(--text-primary) !important;
            padding: 10px 14px;
            font-size: 14px;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--accent-primary) !important;
            box-shadow: var(--shadow-glow) !important;
            background: var(--bg-surface) !important;
            outline: none;
        }

        /* Custom Stats Card Icons */
        .stats-icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4 py-4">

        <!-- Header Halaman -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
            <div>
                <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary)">Master Unit</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="font-size: 12.5px">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-decoration-none" style="color: var(--text-muted)">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" style="color: var(--text-secondary)">Master Unit</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2"
                        id="btnCreateUnit" style="border-radius: var(--radius-md); background: var(--accent-primary); border-color: var(--accent-primary)">
                    <i class="bi bi-plus-lg"></i> Tambah Unit Kerja
                </button>
            </div>
        </div>

        <!-- Kartu Statistik -->
        <div class="row g-3 mb-4 animate-in">
            <div class="col-12 col-md-4">
                <div class="card border-0 px-3 py-3" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-subtle)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon-wrap" style="background: rgba(79, 70, 229, 0.1); color: var(--accent-primary)">
                            <i class="bi bi-hospital"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 12.5px; font-weight: 500">Total Unit Kerja</span>
                            <h3 class="fw-bold mb-0 mt-0.5" data-count="{{ $totalUnits }}" style="color: var(--text-primary)">0</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 px-3 py-3" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-subtle)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon-wrap" style="background: rgba(34, 197, 94, 0.1); color: #22c55e">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 12.5px; font-weight: 500">Unit Status Aktif</span>
                            <h3 class="fw-bold mb-0 mt-0.5" data-count="{{ $activeUnits }}" style="color: var(--text-primary)">0</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 px-3 py-3" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-subtle)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon-wrap" style="background: rgba(239, 68, 68, 0.1); color: #ef4444">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 12.5px; font-weight: 500">Unit Non-Aktif</span>
                            <h3 class="fw-bold mb-0 mt-0.5" data-count="{{ $inactiveUnits }}" style="color: var(--text-primary)">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Utama -->
        <div class="card border-0 animate-in" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm)">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1" style="color: var(--text-primary)">Data Master Unit</h5>
                    <p class="text-muted mb-0" style="font-size: 12.5px">Daftar instalasi rumah sakit penerima sarana & prasarana.</p>
                </div>
                <!-- Custom Search Bar -->
                <div class="position-relative" style="width: 100%; max-width: 280px;">
                    <i class="bi bi-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 13px;"></i>
                    <input type="text" id="tableSearch" class="form-control ps-5" placeholder="Cari unit kerja..." style="height: 38px; border-radius: 50px; font-size: 13px;">
                </div>
            </div>

            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 w-100" id="unitTable">
                        <thead>
                            <tr>
                                <th width="60px" class="text-center">No</th>
                                <th width="120px">Kode Unit</th>
                                <th width="240px">Nama Unit Kerja</th>
                                <th>Deskripsi</th>
                                <th width="120px" class="text-center">Status</th>
                                <th width="120px" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal Form (Tambah / Edit) -->
    <div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="unitModalTitle" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="unitModalTitle" style="color: var(--text-primary)">Tambah Unit Kerja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="unitForm" novalidate>
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="id" id="unitId">

                    <div class="modal-body">
                        <!-- Kode Unit -->
                        <div class="mb-3">
                            <label for="unitCode" class="form-label">Kode Unit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="unitCode" name="code"
                                   placeholder="Contoh: IGD, ICU, RM" required maxlength="20" style="text-transform: uppercase;">
                            <div class="invalid-feedback" id="error-code"></div>
                        </div>

                        <!-- Nama Unit -->
                        <div class="mb-3">
                            <label for="unitName" class="form-label">Nama Unit Kerja <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="unitName" name="name"
                                   placeholder="Contoh: Instalasi Gawat Darurat" required>
                            <div class="invalid-feedback" id="error-name"></div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label for="unitDescription" class="form-label">Deskripsi / Keterangan</label>
                            <textarea class="form-control" id="unitDescription" name="description" rows="3"
                                      placeholder="Tambahkan penjelasan singkat mengenai unit kerja ini (opsional)..."></textarea>
                            <div class="invalid-feedback" id="error-description"></div>
                        </div>

                        <!-- Status Switch -->
                        <div class="mb-1">
                            <label class="form-label d-block">Status Keaktifan</label>
                            <div class="form-check form-switch pt-1">
                                <input class="form-check-input" type="checkbox" role="switch" id="unitActive" name="is_active" value="1" checked style="cursor: pointer;">
                                <label class="form-check-label text-muted" for="unitActive" id="unitActiveLabel" style="font-size: 13.5px; cursor: pointer;">Unit Aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: var(--radius-md)">Batal</button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" id="btnSaveUnit" style="border-radius: var(--radius-md); background: var(--accent-primary); border-color: var(--accent-primary)">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="btnSpinner"></span>
                            <span id="btnText">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Load jQuery, DataTables & SweetAlert2 via CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // CSRF Token Setup untuk AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // 1. Helper Konfigurasi SweetAlert 2 yang responsif terhadap Dark/Light Mode
            function getSwalConfig() {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                return {
                    background: isDark ? '#111318' : '#ffffff',
                    color: isDark ? '#f1f5f9' : '#0f172a',
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#64748b',
                };
            }

            // Toast Config Helper
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // 2. Inisialisasi Yajra DataTable
            const table = $('#unitTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('units.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'code', name: 'code', className: 'fw-bold text-primary' },
                    { data: 'name', name: 'name', className: 'fw-bold' },
                    {
                        data: 'description',
                        name: 'description',
                        render: function(data) {
                            return data ? data : '<em class="text-muted" style="font-size:12.5px;">Tidak ada deskripsi</em>';
                        }
                    },
                    { data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
                ],
                order: [[1, 'asc']], // Urut berdasarkan Kode Unit secara default
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" // Lokalisasi Bahasa Indonesia
                },
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                drawCallback: function() {
                    // Re-apply event listeners when table redraws (pagination, sort, search)
                    initTableRowHighlight();
                }
            });

            // Event untuk Custom Search Input
            $('#tableSearch').keyup(function() {
                table.search($(this).val()).draw();
            });

            // Highlight baris tabel saat dicentang (opsional pendukung dashboard)
            function initTableRowHighlight() {
                $('.row-check').change(function() {
                    $(this).closest('tr').toggleClass('table-active', this.checked);
                });
            }

            // 3. Konfigurasi Label Switch Toggle dalam Modal
            $('#unitActive').change(function() {
                if (this.checked) {
                    $('#unitActiveLabel').text('Unit Aktif').removeClass('text-muted').addClass('text-success');
                } else {
                    $('#unitActiveLabel').text('Unit Non-Aktif').removeClass('text-success').addClass('text-muted');
                }
            });

            // 4. Buka Modal Tambah Unit
            $('#btnCreateUnit').click(function() {
                // Reset form & validasi
                $('#unitForm')[0].reset();
                $('#unitForm .is-invalid').removeClass('is-invalid');
                $('#unitForm .invalid-feedback').text('');

                // Set parameter modal untuk Create
                $('#unitId').val('');
                $('#formMethod').val('POST');
                $('#unitModalTitle').text('Tambah Unit Kerja Baru');
                $('#btnText').text('Simpan Unit');

                // Reset active switch ke default true
                $('#unitActive').prop('checked', true).trigger('change');

                // Tampilkan Modal
                $('#unitModal').modal('show');
            });

            // 5. Submit Form (Tambah / Edit via AJAX)
            $('#unitForm').submit(function(e) {
                e.preventDefault();

                // Bersihkan validasi error yang lama
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                // Loading state
                $('#btnSpinner').removeClass('d-none');
                $('#btnSaveUnit').prop('disabled', true);
                $('#btnText').text('Menyimpan...');

                const unitId = $('#unitId').val();
                const isEdit = unitId !== '';
                const url = isEdit ? `/units/${unitId}` : '/units';
                const method = isEdit ? 'PUT' : 'POST';

                // Serialize form data
                let formData = $(this).serializeArray();

                // Handle checkbox value (since serializeArray won't include unchecked boxes)
                const isActiveIndex = formData.findIndex(item => item.name === 'is_active');
                if (isActiveIndex === -1) {
                    formData.push({ name: 'is_active', value: '0' });
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: $.param(formData),
                    success: function(response) {
                        // Reset loading
                        $('#btnSpinner').addClass('d-none');
                        $('#btnSaveUnit').prop('disabled', false);

                        if (response.success) {
                            // Sembunyikan Modal
                            $('#unitModal').modal('hide');

                            // Tampilkan SweetAlert Toast Sukses
                            Toast.fire({
                                ...getSwalConfig(),
                                icon: 'success',
                                title: response.message
                            });

                            // Reload Datatable (tetap di halaman aktif)
                            table.ajax.reload(null, false);

                            // Perbarui statistik secara asinkron (kalkulasi ulang client-side sederhana)
                            updateStatsWidget(isEdit, formData);
                        }
                    },
                    error: function(xhr) {
                        // Reset loading
                        $('#btnSpinner').addClass('d-none');
                        $('#btnSaveUnit').prop('disabled', false);
                        $('#btnText').text(isEdit ? 'Simpan Perubahan' : 'Simpan Unit');

                        if (xhr.status === 422) {
                            // Tampilkan validasi error dari Laravel
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, messages) {
                                const inputField = $(`[name="${key}"]`);
                                inputField.addClass('is-invalid');
                                $(`#error-${key}`).text(messages[0]);
                            });
                        } else {
                            // Error sistem lainnya
                            Swal.fire({
                                ...getSwalConfig(),
                                icon: 'error',
                                title: 'Gagal Menyimpan!',
                                text: xhr.responseJSON.message || 'Terjadi kesalahan sistem internal.'
                            });
                        }
                    }
                });
            });

            // 6. Ambil Data & Buka Modal Edit Unit
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');

                // Bersihkan validasi error yang lama
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                $.ajax({
                    url: `/units/${id}`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const unit = response.data;

                            // Isi field form modal
                            $('#unitId').val(unit.id);
                            $('#formMethod').val('PUT');
                            $('#unitCode').val(unit.code);
                            $('#unitName').val(unit.name);
                            $('#unitDescription').val(unit.description);

                            // Set status switch
                            $('#unitActive').prop('checked', unit.is_active).trigger('change');

                            // Set parameter modal untuk Edit
                            $('#unitModalTitle').text('Edit Detail Unit Kerja');
                            $('#btnText').text('Simpan Perubahan');

                            // Tampilkan Modal
                            $('#unitModal').modal('show');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            ...getSwalConfig(),
                            icon: 'error',
                            title: 'Koneksi Gagal!',
                            text: 'Gagal mengambil data unit untuk diedit.'
                        });
                    }
                });
            });

            // 7. Hapus Unit Kerja (SweetAlert 2 Konfirmasi)
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');

                Swal.fire({
                    ...getSwalConfig(),
                    title: 'Apakah Anda Yakin?',
                    text: "Data unit kerja akan dihapus (soft-delete) dari sistem!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger px-4 py-2 me-2',
                        cancelButton: 'btn btn-outline-secondary px-4 py-2'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/units/${id}`,
                            type: 'DELETE',
                            success: function(response) {
                                if (response.success) {
                                    Toast.fire({
                                        ...getSwalConfig(),
                                        icon: 'success',
                                        title: response.message
                                    });
                                    table.ajax.reload(null, false);

                                    // Refresh status counter
                                    reloadCounterData();
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    ...getSwalConfig(),
                                    icon: 'error',
                                    title: 'Gagal Menghapus!',
                                    text: xhr.responseJSON.message || 'Unit tidak dapat dihapus karena integritas data.'
                                });
                            }
                        });
                    }
                });
            });

            // 8. Toggle Status Switcher Langsung di Tabel
            $(document).on('change', '.btn-toggle-status', function() {
                const id = $(this).data('id');
                const checkbox = $(this);

                $.ajax({
                    url: `/units/${id}/toggle`,
                    type: 'PATCH',
                    success: function(response) {
                        if (response.success) {
                            Toast.fire({
                                ...getSwalConfig(),
                                icon: 'success',
                                title: response.message
                            });

                            // Refresh status counter
                            reloadCounterData();
                        }
                    },
                    error: function(xhr) {
                        // Revert status switcher jika gagal
                        checkbox.prop('checked', !checkbox.prop('checked'));

                        Swal.fire({
                            ...getSwalConfig(),
                            icon: 'error',
                            title: 'Gagal Mengubah Status!',
                            text: xhr.responseJSON.message || 'Terjadi kesalahan sistem internal.'
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
                    url: "{{ route('units.index') }}",
                    type: 'GET',
                    dataType: 'html', // Panggil halaman biasa
                    success: function(htmlResponse) {
                        // Parsing respons HTML untuk mengambil widget counter yang baru
                        const parsedHtml = $(htmlResponse);

                        $('[data-count]').each(function() {
                            const countName = $(this).closest('.card').find('.text-muted').text().trim();
                            let targetVal = 0;

                            parsedHtml.find('[data-count]').each(function() {
                                const parsedName = $(this).closest('.card').find('.text-muted').text().trim();
                                if (countName === parsedName) {
                                    targetVal = $(this).attr('data-count');
                                    return false; // Break
                                }
                            });

                            // Update data-count attribute
                            $(this).attr('data-count', targetVal);

                            // Re-animate the count number
                            animateCountElement($(this)[0], parseInt(targetVal));
                        });
                    }
                });
            }

            // Re-usable counter number animator
            function animateCountElement(el, target) {
                const duration = 800;
                const steps = 40;
                const stepValue = target / steps;
                let current = 0;
                let step = 0;

                clearInterval(el.timer);

                el.timer = setInterval(() => {
                    step++;
                    current = stepValue * step;
                    if (step >= steps) {
                        current = target;
                        clearInterval(el.timer);
                    }
                    el.textContent = Math.floor(current).toLocaleString();
                }, duration / steps);
            }
        });
    </script>
@endpush
