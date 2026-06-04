{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const API = {
        stats:  '{{ route("api.dashboard.unit.stats") }}',
        charts: '{{ route("api.dashboard.unit.charts") }}',
        recent: '{{ route("api.dashboard.unit.recent") }}',
    };

    const COLORS = {
        primary : '#4f46e5',
        success : '#22c55e',
        warning : '#eab308',
        danger  : '#ef4444',
        info    : '#0ea5e9',
        purple  : '#8b5cf6',
        muted   : '#94a3b8',
        grid    : 'rgba(0,0,0,0.05)',
    };

    const statusMap = {
        pending   : { label: 'Menunggu',   color: COLORS.warning, icon: 'bi-hourglass-split' },
        approved  : { label: 'Disetujui',  color: COLORS.info,    icon: 'bi-check-circle'    },
        partial   : { label: 'Sebagian',   color: '#f97316',      icon: 'bi-dash-circle'     },
        rejected  : { label: 'Ditolak',    color: COLORS.danger,  icon: 'bi-x-circle'        },
        ready     : { label: 'Siap Ambil', color: COLORS.purple,  icon: 'bi-box-seam'        },
        completed : { label: 'Selesai',    color: COLORS.success, icon: 'bi-bag-check'       },
        cancelled : { label: 'Dibatalkan', color: COLORS.muted,   icon: 'bi-slash-circle'    },
    };

    const priorityBadge = {
        normal : '<span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size:11px">Normal</span>',
        urgent : '<span class="badge bg-danger bg-opacity-10 text-danger" style="font-size:11px">Urgent</span>',
    };

    // ── Helpers ──────────────────────────────────────────────────
    function ajax(url) {
        return $.get(url).then(r => r.data);
    }

    function animateCounter(el, target) {
        const $el = $(el);
        const duration = 600;
        const start = performance.now();
        const from = parseInt($el.text()) || 0;

        function step(now) {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3); // easeOutCubic
            $el.text(Math.round(from + (target - from) * eased));
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    // ── 1. Load Stats ───────────────────────────────────────────
    ajax(API.stats).then(data => {
        animateCounter('#stat-pending',   data.pending);
        animateCounter('#stat-approved',  data.approved);
        animateCounter('#stat-ready',     data.ready);
        animateCounter('#stat-completed', data.completed);
    }).fail(() => {
        ['pending','approved','ready','completed'].forEach(k => {
            $(`#stat-${k}`).text('!').css('color', COLORS.danger);
        });
    });

    // ── 2. Load Charts ──────────────────────────────────────────
    ajax(API.charts).then(data => {

        // 2a. Monthly Requests — Bar chart
        const monthlyCtx = document.getElementById('chartMonthlyRequests');
        if (monthlyCtx) {
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: data.monthly_requests.map(m => m.label),
                    datasets: [{
                        label: 'Jumlah Permintaan',
                        data: data.monthly_requests.map(m => m.value),
                        backgroundColor: COLORS.primary + '22',
                        borderColor: COLORS.primary,
                        borderWidth: 2,
                        borderRadius: 8,
                        maxBarThickness: 48,
                    }],
                },
                options: chartOpts('bar'),
            });
        }

        // 2b. Status Distribution — Doughnut chart
        const statusCtx = document.getElementById('chartStatusDist');
        if (statusCtx) {
            const labels = Object.keys(data.status_dist);
            const values = Object.values(data.status_dist);
            const colors = labels.map(l => {
                const found = Object.values(statusMap).find(s => s.label === l);
                return found ? found.color : COLORS.muted;
            });

            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 0,
                        hoverOffset: 8,
                    }],
                },
                options: {
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            cornerRadius: 8,
                            titleFont: { family: "'Plus Jakarta Sans'", weight: '600' },
                            bodyFont:  { family: "'Plus Jakarta Sans'" },
                            padding: 12,
                        },
                    },
                },
            });

            // Custom Legend
            const legendHtml = labels.map((l, i) => `
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width:10px;height:10px;border-radius:3px;background:${colors[i]};display:inline-block"></span>
                        <span style="color:var(--text-secondary)">${l}</span>
                    </div>
                    <span class="fw-bold" style="color:var(--text-primary)">${values[i]}</span>
                </div>
            `).join('');
            $('#statusLegend').html(legendHtml);
        }
    });

    // ── 3. Load Recent Requests ─────────────────────────────────
    ajax(API.recent).then(items => {
        const $c = $('#recentRequestsContainer');
        if (!items.length) {
            $c.html('<div class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada permintaan.</div>');
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-hover align-middle mb-0" style="font-size:13px">';
        html += `<thead><tr style="color:var(--text-muted);font-size:11.5px;text-transform:uppercase;letter-spacing:.5px">
            <th class="border-0 pb-2">No. Permintaan</th>
            <th class="border-0 pb-2">Tanggal</th>
            <th class="border-0 pb-2">Item</th>
            <th class="border-0 pb-2">Prioritas</th>
            <th class="border-0 pb-2">Status</th>
            <th class="border-0 pb-2"></th>
        </tr></thead><tbody>`;

        items.forEach(r => {
            const s = statusMap[r.status] || { label: r.status, color: COLORS.muted, icon: 'bi-circle' };
            html += `<tr>
                <td class="fw-semibold" style="color:var(--text-primary)">${r.request_number}</td>
                <td style="color:var(--text-secondary)">${r.request_date}</td>
                <td style="color:var(--text-secondary)">${r.items_count} barang</td>
                <td>${priorityBadge[r.priority] || r.priority}</td>
                <td>
                    <span class="badge rounded-pill" style="background:${s.color}22;color:${s.color};font-size:11px">
                        <i class="bi ${s.icon} me-1"></i>${s.label}
                    </span>
                </td>
                <td class="text-end">
                    <a href="${r.url}" class="btn btn-sm btn-outline-primary" style="border-radius:var(--radius-sm);font-size:11.5px;padding:4px 12px">
                        <i class="bi bi-eye me-1"></i>Detail
                    </a>
                </td>
            </tr>`;
        });

        html += '</tbody></table></div>';
        $c.html(html);
    }).fail(() => {
        $('#recentRequestsContainer').html('<div class="text-center py-3 text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Gagal memuat data.</div>');
    });

    // ── Chart Options Helper ────────────────────────────────────
    function chartOpts(type) {
        return {
            responsive: true,
            maintainAspectRatio: type === 'bar',
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    cornerRadius: 8,
                    titleFont: { family: "'Plus Jakarta Sans'", weight: '600' },
                    bodyFont:  { family: "'Plus Jakarta Sans'" },
                    padding: 12,
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: "'Plus Jakarta Sans'", size: 11 }, color: COLORS.muted },
                    border: { display: false },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: COLORS.grid },
                    ticks: {
                        font: { family: "'Plus Jakarta Sans'", size: 11 },
                        color: COLORS.muted,
                        stepSize: 1,
                        precision: 0,
                    },
                    border: { display: false },
                },
            },
        };
    }

});
</script>
