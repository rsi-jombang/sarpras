<script>
document.addEventListener('DOMContentLoaded', function () {

    // =========================================================================
    // 1. Hitung ringkasan otomatis setiap kali qty-approved berubah
    // =========================================================================
    function recalcSummary() {
        let full = 0, partial = 0, rejected = 0;

        $('.review-row').each(function () {
            const requested = parseInt($(this).data('requested'));
            const approved  = parseInt($(this).find('.qty-approved').val()) || 0;

            if (approved <= 0) {
                rejected++;
            } else if (approved < requested) {
                partial++;
            } else {
                full++;
            }
        });

        $('#countFull').text(full);
        $('#countPartial').text(partial);
        $('#countRejected').text(rejected);

        // Tentukan aksi otomatis
        const totalRows = full + partial + rejected;
        let action, bgStyle, iconClass, textClass, label;

        if (rejected === totalRows) {
            // Semua ditolak
            action    = 'reject';
            bgStyle   = 'background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3)';
            iconClass = 'bi-x-circle text-danger';
            textClass = 'text-danger';
            label     = 'Akan ditolak semua';
        } else if (partial > 0 || (rejected > 0 && rejected < totalRows)) {
            // Sebagian disetujui
            action    = 'partial';
            bgStyle   = 'background:rgba(14,165,233,0.1);border:1px solid rgba(14,165,233,0.3)';
            iconClass = 'bi-dash-circle text-info';
            textClass = 'text-info';
            label     = 'Akan disetujui sebagian';
        } else {
            // Semua disetujui penuh
            action    = 'approve';
            bgStyle   = 'background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3)';
            iconClass = 'bi-check-circle text-success';
            textClass = 'text-success';
            label     = 'Akan disetujui penuh';
        }

        // Override jika checkbox "Tolak semua" aktif
        if ($('#chkRejectAll').is(':checked')) {
            action    = 'reject';
            bgStyle   = 'background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3)';
            iconClass = 'bi-x-circle text-danger';
            textClass = 'text-danger';
            label     = 'Akan ditolak semua';
        }

        $('#reviewAction').val(action);
        $('#actionPreview').attr('style', bgStyle + ';font-size:13px').html(
            `<i class="bi ${iconClass} me-1"></i><span class="fw-semibold ${textClass}">${label}</span>`
        );

        // Catatan wajib saat reject
        if (action === 'reject') {
            $('#noteRequired').removeClass('d-none');
            $('#reviewNotes').attr('required', true);
        } else {
            $('#noteRequired').addClass('d-none');
            $('#reviewNotes').removeAttr('required');
        }
    }

    // =========================================================================
    // 2. Event listeners
    // =========================================================================

    // Saat input qty approved berubah
    $(document).on('input change', '.qty-approved', function () {
        const $row      = $(this).closest('.review-row');
        const requested = parseInt($row.data('requested'));
        let val         = parseInt($(this).val());

        // Clamp: min=0, max=requested
        if (isNaN(val) || val < 0) val = 0;
        if (val > requested) val = requested;
        $(this).val(val);

        // Jika qty = 0, aktifkan dan highlight field alasan tolak
        const $reason = $row.find('.rejection-reason');
        if (val <= 0) {
            $reason.attr('placeholder', 'Wajib isi alasan...')
                   .addClass('border-danger');
        } else {
            $reason.attr('placeholder', 'Jika ditolak...')
                   .removeClass('border-danger');
        }

        recalcSummary();
    });

    // Checkbox "Tolak seluruh permintaan"
    $('#chkRejectAll').on('change', function () {
        const isChecked = $(this).is(':checked');

        if (isChecked) {
            // Set semua qty ke 0 dan disable
            $('.qty-approved').val(0).prop('disabled', true);
            $('.rejection-reason')
                .attr('placeholder', 'Ditolak via tolak semua')
                .prop('disabled', true)
                .addClass('border-danger');
        } else {
            // Restore: set kembali ke qty diminta dan enable
            $('.review-row').each(function () {
                const requested = parseInt($(this).data('requested'));
                $(this).find('.qty-approved').val(requested).prop('disabled', false);
                $(this).find('.rejection-reason')
                    .attr('placeholder', 'Jika ditolak...')
                    .prop('disabled', false)
                    .removeClass('border-danger');
            });
        }

        recalcSummary();
    });

    // =========================================================================
    // 3. Submit form dengan konfirmasi SweetAlert
    // =========================================================================
    $('#reviewForm').on('submit', function (e) {
        e.preventDefault();

        const action = $('#reviewAction').val();
        const isReject = action === 'reject';

        // Validasi: jika reject, catatan wajib
        if (isReject && !$('#reviewNotes').val().trim()) {
            Swal.fire({
                icon: 'warning',
                title: 'Catatan Diperlukan',
                text: 'Alasan penolakan wajib diisi di kolom Catatan Review.',
                confirmButtonColor: 'var(--accent-primary)',
            });
            $('#reviewNotes').focus();
            return;
        }

        // Validasi: jika ada item qty=0, pastikan ada alasan
        let missingReason = false;
        if (!isReject) {
            $('.review-row').each(function () {
                const qty    = parseInt($(this).find('.qty-approved').val()) || 0;
                const reason = $(this).find('.rejection-reason').val()?.trim();
                if (qty <= 0 && !reason) {
                    missingReason = true;
                    $(this).find('.rejection-reason').addClass('border-danger').focus();
                    return false; // break loop
                }
            });
        }

        if (missingReason) {
            Swal.fire({
                icon: 'warning',
                title: 'Alasan Belum Diisi',
                text: 'Setiap item yang ditolak (jumlah = 0) wajib memiliki alasan.',
                confirmButtonColor: 'var(--accent-primary)',
            });
            return;
        }

        // Konfirmasi
        const confirmMap = {
            approve: {
                title: 'Setujui Permintaan?',
                text: 'Semua item akan disetujui sesuai jumlah yang tertera.',
                icon: 'question',
                confirmText: 'Ya, Setujui',
                confirmColor: '#22c55e',
            },
            partial: {
                title: 'Setujui Sebagian?',
                text: 'Beberapa item disetujui dengan jumlah berbeda atau ditolak.',
                icon: 'question',
                confirmText: 'Ya, Proses',
                confirmColor: '#0ea5e9',
            },
            reject: {
                title: 'Tolak Permintaan?',
                text: 'Seluruh permintaan akan ditolak. Tindakan ini tidak bisa dibatalkan.',
                icon: 'warning',
                confirmText: 'Ya, Tolak',
                confirmColor: '#ef4444',
            },
        };

        const cfg = confirmMap[action];

        Swal.fire({
            title: cfg.title,
            text: cfg.text,
            icon: cfg.icon,
            showCancelButton: true,
            confirmButtonText: cfg.confirmText,
            cancelButtonText: 'Batal',
            confirmButtonColor: cfg.confirmColor,
            reverseButtons: true,
        }).then(result => {
            if (result.isConfirmed) {
                // Re-enable disabled fields agar terkirim ke server
                $('.qty-approved, .rejection-reason').prop('disabled', false);

                // Submit form secara native
                document.getElementById('reviewForm').submit();
            }
        });
    });

    // =========================================================================
    // 4. Inisialisasi awal
    // =========================================================================
    recalcSummary();

});
</script>
