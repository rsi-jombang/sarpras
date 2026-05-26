// 1. Helper Konfigurasi SweetAlert 2 yang responsif terhadap Dark/Light Mode
window.getSwalConfig = function() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    return {
        background: isDark ? '#111318' : '#ffffff',
        color: isDark ? '#f1f5f9' : '#0f172a',
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#64748b',
    };
}

// 2. Toast Config Helper
window.Toast = Swal.mixin({
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

// 3. Re-usable counter number animator
window.animateCountElement = function(el, target) {
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

// 4. Konfigurasi default bahasa DataTables secara global
if ($.fn.dataTable) {
    $.extend(true, $.fn.dataTable.defaults, {
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            emptyTable: 'Belum ada data',
            zeroRecords: 'Data tidak ditemukan',
            lengthMenu: 'Tampilkan _MENU_ baris',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
            paginate: {
                first: 'Pertama',
                last: 'Terakhir',
                next: 'Berikutnya',
                previous: 'Sebelumnya'
            }
        },
        // Opsi ini menambahkan animasi baris muncul pada DataTables secara default
        drawCallback: function(settings) {
            $(settings.aoData).each(function() {
                $(this.anims).remove();
                var node = this.nTr;
                if (node) {
                    node.style.setProperty("animation", "none", "important");
                    node.style.display = "none";
                    setTimeout(function() {
                        node.style.display = "table-row";
                        node.style.setProperty("animation", "fadeIn", "important");
                    }, 100);
                }
            });
        }
    });
}
