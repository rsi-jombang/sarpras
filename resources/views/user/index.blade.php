@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="container-fluid px-4 py-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
            <div>
                <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary)">Manajemen Pengguna</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="font-size: 12.5px">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-decoration-none" style="color: var(--text-muted)">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" style="color: var(--text-secondary)">Pengguna</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2"
                        id="btnCreateUser" style="border-radius: var(--radius-md); background: var(--accent-primary); border-color: var(--accent-primary)">
                    <i class="bi bi-person-plus"></i> Tambah Pengguna
                </button>
            </div>
        </div>

        <div class="row g-3 mb-4 animate-in">
            <div class="col-6 col-md-3">
                <div class="card border-0 px-3 py-3" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-subtle)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon-wrap" style="background: rgba(79, 70, 229, 0.1); color: var(--accent-primary)">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 12.5px; font-weight: 500">Total Pengguna</span>
                            <h3 class="fw-bold mb-0 mt-0.5" data-count="{{ $totalUsers }}" style="color: var(--text-primary)">0</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 px-3 py-3" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-subtle)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon-wrap" style="background: rgba(239, 68, 68, 0.1); color: #ef4444">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 12.5px; font-weight: 500">Administrator</span>
                            <h3 class="fw-bold mb-0 mt-0.5" data-count="{{ $adminCount }}" style="color: var(--text-primary)">0</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 px-3 py-3" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-subtle)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon-wrap" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 12.5px; font-weight: 500">Sarpras</span>
                            <h3 class="fw-bold mb-0 mt-0.5" data-count="{{ $sarprasCount }}" style="color: var(--text-primary)">0</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 px-3 py-3" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-subtle)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon-wrap" style="background: rgba(100, 116, 139, 0.1); color: #64748b">
                            <i class="bi bi-building"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 12.5px; font-weight: 500">Unit</span>
                            <h3 class="fw-bold mb-0 mt-0.5" data-count="{{ $unitCount }}" style="color: var(--text-primary)">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 animate-in" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm)">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1" style="color: var(--text-primary)">Data Pengguna</h5>
                    <p class="text-muted mb-0" style="font-size: 12.5px">Daftar seluruh pengguna yang memiliki akses ke sistem.</p>
                </div>
                <div class="position-relative" style="width: 100%; max-width: 280px;">
                    <i class="bi bi-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 13px;"></i>
                    <input type="text" id="tableSearch" class="form-control ps-5" placeholder="Cari pengguna..." style="height: 38px; border-radius: 50px; font-size: 13px;">
                </div>
            </div>

            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 w-100" id="userTable">
                        <thead>
                            <tr>
                                <th width="50px" class="text-center">No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th width="80px">NIP/NIK</th>
                                <th width="100px">Role</th>
                                <th>Unit</th>
                                <th width="100px" class="text-center">Status</th>
                                <th width="100px" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalTitle" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="userModalTitle" style="color: var(--text-primary)">Tambah Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="userForm" novalidate>
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="id" id="userId">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="userName" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="userName" name="name" placeholder="Contoh: Dr. Andi Pratama" required>
                            <div class="invalid-feedback" id="error-name"></div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="userUsername" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="userUsername" name="username" placeholder="Contoh: dr.andi" required>
                                <div class="invalid-feedback" id="error-username"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="userPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="userPassword" name="password" placeholder="Min. 8 karakter" minlength="8">
                                <div class="invalid-feedback" id="error-password"></div>
                                <small class="text-muted" id="passwordHelp" style="font-size:11px">Kosongkan jika tidak ingin mengubah</small>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="userEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="userEmail" name="email" placeholder="email@rs.com">
                                <div class="invalid-feedback" id="error-email"></div>
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="userEmployeeId" class="form-label">NIP / NIK</label>
                                <input type="text" class="form-control" id="userEmployeeId" name="employee_id" placeholder="Contoh: 198701012010011001">
                                <div class="invalid-feedback" id="error-employee_id"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="userPhone" class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" id="userPhone" name="phone" placeholder="Contoh: 08123456789">
                                <div class="invalid-feedback" id="error-phone"></div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="userRole" class="form-label">Role / Hak Akses <span class="text-danger">*</span></label>
                                <select class="form-select" id="userRole" name="role" required>
                                    <option value="">Pilih role...</option>
                                    <option value="admin">Administrator</option>
                                    <option value="sarpras">Sarpras</option>
                                    <option value="unit">Unit / Departemen</option>
                                </select>
                                <div class="invalid-feedback" id="error-role"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="userUnit" class="form-label">Unit Kerja</label>
                                <select class="form-select" id="userUnit" name="unit_id">
                                    <option value="">— Tidak Ada —</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="error-unit_id"></div>
                            </div>
                        </div>

                        <div class="mb-1">
                            <label class="form-label d-block">Status Keaktifan</label>
                            <div class="form-check form-switch pt-1">
                                <input class="form-check-input" type="checkbox" role="switch" id="userActive" name="is_active" value="1" checked style="cursor: pointer;">
                                <label class="form-check-label text-muted" for="userActive" id="userActiveLabel" style="font-size: 13.5px; cursor: pointer;">Akun Aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: var(--radius-md)">Batal</button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" id="btnSaveUser" style="border-radius: var(--radius-md); background: var(--accent-primary); border-color: var(--accent-primary)">
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
        document.addEventListener('DOMContentLoaded', function () {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            const table = $('#userTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.index') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'name', name: 'name', className: 'fw-bold' },
                        { data: 'username', name: 'username' },
                        { data: 'email', name: 'email' },
                        { data: 'employee_id', name: 'employee_id', defaultContent: '<em class="text-muted" style="font-size:12.5px">—</em>' },
                        { data: 'role_badge', name: 'role', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'unit_name', name: 'unit.name', orderable: false },
                        { data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' },
                    ],
                order: [[1, 'asc']],
                language: { url: "https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" },
                createdRow: function (row, data) {
                    if (data.is_active == 0) {
                        $(row).css('background-color', 'rgba(255,0,0,0.03)');
                        $(row).find('td').css('opacity', '0.7');
                    }
                },
            });

            $('#tableSearch').keyup(function () {
                table.search($(this).val()).draw();
            });

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
                timerProgressBar: true,
            });

            function getRoleHelpText(role) {
                const help = {
                    'admin': 'Akses penuh ke seluruh fitur sistem, termasuk manajemen pengguna & pengaturan.',
                    'sarpras': 'Kelola barang, stok, permintaan, dan pembelian. Tidak bisa manajemen pengguna.',
                    'unit': 'Hanya bisa mengajukan permintaan barang dan melihat dashboard unit sendiri.',
                };
                return help[role] || '';
            }

            $('#userRole').on('change', function () {
                const role = $(this).val();
                const help = getRoleHelpText(role);
                const $help = $('#roleHelp');
                if (help) {
                    if ($help.length) $help.text(help);
                    else $(this).closest('.col-md-6').append(
                        '<small class="text-muted" id="roleHelp" style="font-size:11px">' + help + '</small>'
                    );
                } else {
                    $help?.remove();
                }
            });

            $('#btnCreateUser').click(function () {
                $('#userModalTitle').text('Tambah Pengguna Baru');
                $('#formMethod').val('POST');
                $('#userId').val('');
                $('#userPassword').prop('required', true).prop('placeholder', 'Min. 8 karakter');
                $('#passwordHelp').hide();
                $('#userRole').val('').trigger('change');
                $('#userForm')[0].reset();
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#userActive').prop('checked', true);
                $('#userModal').modal('show');
            });

            $('#userForm').submit(function (e) {
                e.preventDefault();
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#btnSpinner').removeClass('d-none');
                $('#btnSaveUser').prop('disabled', true);

                const userId = $('#userId').val();
                const isEdit = userId !== '';
                const url = isEdit ? `/users/${userId}` : '/users';
                const method = isEdit ? 'PUT' : 'POST';

                let formData = $(this).serializeArray();
                if (formData.findIndex(item => item.name === 'is_active') === -1) {
                    formData.push({ name: 'is_active', value: '0' });
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: $.param(formData),
                    success: function (response) {
                        $('#btnSpinner').addClass('d-none');
                        $('#btnSaveUser').prop('disabled', false);
                        if (response.success) {
                            $('#userModal').modal('hide');
                            Toast.fire({ ...getSwalConfig(), icon: 'success', title: response.message });
                            table.ajax.reload(null, false);
                            reloadCounterData();
                        }
                    },
                    error: function (xhr) {
                        $('#btnSpinner').addClass('d-none');
                        $('#btnSaveUser').prop('disabled', false);
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function (key, messages) {
                                $(`[name="${key}"]`).addClass('is-invalid');
                                $(`#error-${key}`).text(messages[0]);
                            });
                        }
                    },
                });
            });

            $(document).on('click', '.btn-edit', function () {
                const id = $(this).data('id');

                $.get(`/users/${id}`, function (response) {
                    if (!response.success) return;
                    const user = response.data;

                    $('#userModalTitle').text('Edit Pengguna: ' + user.name);
                    $('#formMethod').val('PUT');
                    $('#userId').val(user.id);
                    $('#userName').val(user.name);
                    $('#userUsername').val(user.username);
                    $('#userEmail').val(user.email);
                    $('#userPassword').prop('required', false).val('').prop('placeholder', 'Kosongkan jika tidak diubah');
                    $('#passwordHelp').show();
                    $('#userEmployeeId').val(user.employee_id || '');
                    $('#userPhone').val(user.phone || '');
                    $('#userRole').val(user.role).trigger('change');
                    $('#userUnit').val(user.unit_id || '');
                    $('#userActive').prop('checked', user.is_active);
                    $('.form-control, .form-select').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                    $('#userModal').modal('show');
                });
            });

            $(document).on('click', '.btn-delete', function () {
                const id = $(this).data('id');

                Swal.fire({
                    ...getSwalConfig(),
                    title: 'Hapus Pengguna?',
                    text: 'Pengguna ini akan dihapus secara permanen dari sistem!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger px-4 py-2 me-2',
                        cancelButton: 'btn btn-outline-secondary px-4 py-2',
                    },
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/users/${id}`,
                            type: 'DELETE',
                            success: function (response) {
                                if (response.success) {
                                    Toast.fire({ ...getSwalConfig(), icon: 'success', title: response.message });
                                    table.ajax.reload(null, false);
                                    reloadCounterData();
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    ...getSwalConfig(),
                                    icon: 'error',
                                    title: 'Gagal Menghapus!',
                                    text: xhr.responseJSON.message || 'Terjadi kesalahan.',
                                });
                            },
                        });
                    }
                });
            });

            $(document).on('change', '.btn-toggle-status', function () {
                const id = $(this).data('id');
                const checkbox = $(this);

                $.ajax({
                    url: `/users/${id}/toggle`,
                    type: 'PATCH',
                    success: function (response) {
                        if (response.success) {
                            Toast.fire({ ...getSwalConfig(), icon: 'success', title: response.message });
                            reloadCounterData();
                        }
                    },
                    error: function () {
                        checkbox.prop('checked', !checkbox.prop('checked'));
                        Swal.fire({
                            ...getSwalConfig(),
                            icon: 'error',
                            title: 'Gagal Mengubah Status!',
                        });
                    },
                });
            });

            function reloadCounterData() {
                $.ajax({
                    url: "{{ route('users.index') }}",
                    type: 'GET',
                    dataType: 'html',
                    success: function (htmlResponse) {
                        const parsedHtml = $(htmlResponse);
                        $('[data-count]').each(function () {
                            const $card = $(this).closest('.card');
                            const countName = $card.find('.text-muted').text().trim();
                            let targetVal = 0;
                            parsedHtml.find('[data-count]').each(function () {
                                const parsedName = $(this).closest('.card').find('.text-muted').text().trim();
                                if (countName === parsedName) {
                                    targetVal = $(this).attr('data-count');
                                    return false;
                                }
                            });
                            $(this).attr('data-count', targetVal);
                            if (window.animateCountElement) {
                                window.animateCountElement($(this)[0], parseInt(targetVal));
                            }
                        });
                    },
                });
            }
        });
    </script>
@endpush
