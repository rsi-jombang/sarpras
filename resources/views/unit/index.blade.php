@extends('layouts.app')

@section('title', 'Master Unit')

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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // CSRF Token Setup untuk AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // 1. INIT DATATABLE
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
                order: [[1, 'asc']],
                createdRow: function(row, data, dataIndex) {
                    if (data.is_active == 0) {
                        $(row).css('background-color', 'rgba(255,0,0,0.03)');
                        $(row).find('td').css('opacity', '0.7');
                    }
                }
            });

            // 3. Custom Search Input Logic
            $('#tableSearch').keyup(function() {
                table.search($(this).val()).draw();
            });

            // Helper Konfigurasi SweetAlert 2
            function getSwalConfig() {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                return {
                    background: isDark ? '#111318' : '#ffffff',
                    color: isDark ? '#f1f5f9' : '#0f172a',
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#64748b',
                };
            }

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            $('#btnCreateUnit').click(function () {
                $('#unitModalTitle').text('Tambah Unit Kerja');
                $('#formMethod').val('POST');
                $('#unitId').val('');
                $('#unitForm')[0].reset();
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#unitActive').prop('checked', true);
                $('#unitModal').modal('show');
            });

            $(document).on('click', '.btn-edit', function () {
                const id = $(this).data('id');

                $.get(`/units/${id}`, function (response) {
                    if (!response.success) return;
                    const unit = response.data;

                    $('#unitModalTitle').text('Edit Unit: ' + unit.name);
                    $('#formMethod').val('PUT');
                    $('#unitId').val(unit.id);
                    $('#unitCode').val(unit.code);
                    $('#unitName').val(unit.name);
                    $('#unitDescription').val(unit.description || '');
                    $('#unitActive').prop('checked', unit.is_active);
                    $('.form-control').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                    $('#unitModal').modal('show');
                });
            });

            // Submit Form (Tambah / Edit)
            $('#unitForm').submit(function(e) {
                e.preventDefault();
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#btnSpinner').removeClass('d-none');
                $('#btnSaveUnit').prop('disabled', true);

                const unitId = $('#unitId').val();
                const isEdit = unitId !== '';
                const url = isEdit ? `/units/${unitId}` : '/units';
                const method = isEdit ? 'PUT' : 'POST';
                let formData = $(this).serializeArray();
                if (formData.findIndex(item => item.name === 'is_active') === -1) {
                    formData.push({ name: 'is_active', value: '0' });
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: $.param(formData),
                    success: function(response) {
                        $('#btnSpinner').addClass('d-none');
                        $('#btnSaveUnit').prop('disabled', false);
                        if (response.success) {
                            $('#unitModal').modal('hide');
                            Toast.fire({...getSwalConfig(), icon: 'success', title: response.message});
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        $('#btnSpinner').addClass('d-none');
                        $('#btnSaveUnit').prop('disabled', false);
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function(key, messages) {
                                $(`[name="${key}"]`).addClass('is-invalid');
                                $(`#error-${key}`).text(messages[0]);
                            });
                        }
                    }
                });
            });

            // Hapus Unit
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
                            window.animateCountElement($(this)[0], parseInt(targetVal));
                        });
                    }
                });
            }
        });
    </script>
@endpush
