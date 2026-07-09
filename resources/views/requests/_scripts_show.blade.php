<script>
document.addEventListener('DOMContentLoaded', function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const REQ_ID = {{ $request->id }};
    const API    = '/api/requests';
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

    // -------------------------------------------------------------------------
    // Submit — unit kirim draft ke sarpras
    // -------------------------------------------------------------------------
    $('#btnSubmit').on('click', function () {
        Swal.fire({
            ...swalCfg(),
            title: 'Kirim Permintaan?',
            text: 'Draft akan dikirim ke Sarpras untuk direview.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-send"></i> Ya, Kirim',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: 'btn btn-success text-white px-4 py-2 me-2', cancelButton: 'btn btn-outline-secondary px-4 py-2' },
            buttonsStyling: false,
        }).then(res => {
            if (!res.isConfirmed) return;
            $.post(`${API}/${REQ_ID}/submit`, {}, data => {
                if (data.success) {
                    Toast.fire({ ...swalCfg(), icon: 'success', title: data.message });
                    setTimeout(() => location.reload(), 1200);
                }
            }).fail(xhr => {
                Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal mengirim permintaan.' });
            });
        });
    });

    // -------------------------------------------------------------------------
    // Fulfill
    // -------------------------------------------------------------------------
    $('#btnFulfill').on('click', function () {
        Swal.fire({
            ...swalCfg(),
            title: 'Siapkan Barang?',
            text: 'Stok akan dikurangi dan status berubah menjadi "Siap Diambil".',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-box-arrow-in-down"></i> Ya, Siapkan',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: 'btn btn-info text-white px-4 py-2 me-2', cancelButton: 'btn btn-outline-secondary px-4 py-2' },
            buttonsStyling: false,
        }).then(res => {
            if (!res.isConfirmed) return;
            $.post(`${API}/${REQ_ID}/fulfill`, {}, data => {
                if (data.success) {
                    Toast.fire({ ...swalCfg(), icon: 'success', title: data.message });
                    setTimeout(() => location.reload(), 1200);
                }
            }).fail(xhr => {
                Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal menyiapkan barang.' });
            });
        });
    });

    // -------------------------------------------------------------------------
    // Complete — unit konfirmasi ambil
    // -------------------------------------------------------------------------
    $('#btnComplete').on('click', () => $('#completeModal').modal('show'));

    $('#completeForm').on('submit', function (e) {
        e.preventDefault();
        const name = $('#receivedByName').val().trim();
        if (!name) { $('#receivedByName').addClass('is-invalid'); return; }
        $('#receivedByName').removeClass('is-invalid');
        $('#btnCompleteSpinner').removeClass('d-none');

        $.post(`${API}/${REQ_ID}/complete`, { received_by_name: name }, data => {
            $('#btnCompleteSpinner').addClass('d-none');
            if (data.success) {
                $('#completeModal').modal('hide');
                Toast.fire({ ...swalCfg(), icon: 'success', title: data.message });
                setTimeout(() => location.reload(), 1200);
            }
        }).fail(xhr => {
            $('#btnCompleteSpinner').addClass('d-none');
            Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal konfirmasi.' });
        });
    });

    // -------------------------------------------------------------------------
    // Cancel
    // -------------------------------------------------------------------------
    $('#btnCancel').on('click', function () {
        Swal.fire({
            ...swalCfg(),
            title: 'Batalkan Permintaan?',
            @php
                $isDraft = $request->status->value === 'draft';
            @endphp
            text: '{{ $isDraft ? "Draft ini akan dibatalkan dan tidak akan dikirim ke Sarpras." : "Permintaan ini tidak dapat dibatalkan setelah dikonfirmasi." }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-x-lg"></i> Ya, Batalkan',
            cancelButtonText: 'Tidak',
            customClass: { confirmButton: 'btn btn-danger px-4 py-2 me-2', cancelButton: 'btn btn-outline-secondary px-4 py-2' },
            buttonsStyling: false,
        }).then(res => {
            if (!res.isConfirmed) return;
            $.post(`${API}/${REQ_ID}/cancel`, {}, data => {
                if (data.success) {
                    Toast.fire({ ...swalCfg(), icon: 'success', title: data.message });
                    setTimeout(() => window.location.href = '{{ route("requests.index") }}', 1200);
                }
            }).fail(xhr => {
                Toast.fire({ ...swalCfg(), icon: 'error', title: xhr.responseJSON?.message || 'Gagal membatalkan.' });
            });
        });
    });

});
</script>
