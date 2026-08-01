{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const API = {
        stats:  '{{ route("api.dashboard.admin.stats") }}',
        charts: '{{ route("api.dashboard.admin.charts") }}',
    };

    const COLORS = {
        primary : '#4f46e5',
        success : '#22c55e',
        warning : '#eab308',
        danger  : '#ef4444',
        info    : '#0ea5e9',
        purple  : '#8b5cf6',
        orange  : '#f97316',
        slate   : '#64748b',
        indigo  : '#6366f1',
        muted   : '#94a3b8',
        grid    : 'rgba(0,0,0,0.05)',
    };

    // ── Chart instances (untuk di-destroy saat re-render) ─────
    const charts = {
        monthlyRequests: null,
        monthlyPOs     : null,
        statusDist     : null,
        topUnits       : null,
    };

    // ── Helpers ──────────────────────────────────────────────────
    function buildUrl(base) {
        const m = $('#filterMonth').val();
        const y = $('#filterYear').val();
        return `${base}?month=${m}&year=${y}`;
    }

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
            const eased = 1 - Math.pow(1 - progress, 3);
            $el.text(Math.round(from + (target - from) * eased));
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    function formatCurrency(val) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
    }

    function chartBaseOpts(withLegend = false) {
        const opts = {
            responsive: true,
            maintainAspectRatio: true,
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
                    ticks: { font: { family: "'Plus Jakarta Sans'", size: 11 }, color: COLORS.muted, precision: 0 },
                    border: { display: false },
                },
            },
        };

        if (withLegend) {
            opts.plugins.legend = {
                display: true,
                position: 'top',
                align: 'end',
                labels: {
                    usePointStyle: true,
                    pointStyle: 'rectRounded',
                    boxWidth: 10,
                    boxHeight: 10,
                    padding: 16,
                    font: { family: "'Plus Jakarta Sans'", size: 11.5 },
                    color: COLORS.muted,
                },
            };
        }

        return opts;
    }

    // ── 1. Load Stats ───────────────────────────────────────────
    function loadStats() {
        ajax(buildUrl(API.stats)).then(data => {
            const keys = [
                'total_requests', 'pending_requests', 'completed_requests',
                'total_items', 'low_stock_items', 'total_pos',
                'total_users', 'total_units',
            ];
            keys.forEach(k => {
                const id = '#stat-' + k.replace(/_/g, '-');
                if (data[k] !== undefined) animateCounter(id, data[k]);
            });
        }).fail(() => {
            $('[id^="stat-"]').text('!').css('color', COLORS.danger);
        });
    }

    // ── 2. Render Charts ────────────────────────────────────────
    function renderCharts(data) {

        // 2a. Monthly Requests (12 bulan) — Line + Bar combo
        const monthlyCtx = document.getElementById('chartMonthlyRequests');
        if (monthlyCtx) {
            if (charts.monthlyRequests) charts.monthlyRequests.destroy();
            const opts = chartBaseOpts(true);

            charts.monthlyRequests = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: data.monthly_requests.map(m => m.label),
                    datasets: [
                        {
                            label: 'Total Permintaan',
                            data: data.monthly_requests.map(m => m.total),
                            backgroundColor: COLORS.primary + '22',
                            borderColor: COLORS.primary,
                            borderWidth: 2,
                            borderRadius: 6,
                            maxBarThickness: 28,
                            order: 2,
                        },
                        {
                            label: 'Selesai',
                            type: 'line',
                            data: data.monthly_requests.map(m => m.completed),
                            borderColor: COLORS.success,
                            backgroundColor: COLORS.success + '15',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            borderWidth: 2,
                            order: 1,
                        },
                    ],
                },
                options: opts,
            });
        }

        // 2b. Monthly POs — Mixed (Bar for count, Line for value)
        const poCtx = document.getElementById('chartMonthlyPOs');
        if (poCtx) {
            if (charts.monthlyPOs) charts.monthlyPOs.destroy();
            const opts = chartBaseOpts(true);

            opts.scales.y1 = {
                position: 'right',
                beginAtZero: true,
                grid: { display: false },
                ticks: {
                    font: { family: "'Plus Jakarta Sans'", size: 11 },
                    color: COLORS.muted,
                    callback: val => val >= 1_000_000 ? (val / 1_000_000) + ' Jt' : val >= 1_000 ? (val / 1_000) + ' Rb' : val,
                },
                border: { display: false },
            };

            charts.monthlyPOs = new Chart(poCtx, {
                type: 'bar',
                data: {
                    labels: data.monthly_pos.map(m => m.label),
                    datasets: [
                        {
                            label: 'Jumlah PO',
                            data: data.monthly_pos.map(m => m.total),
                            backgroundColor: COLORS.purple + '22',
                            borderColor: COLORS.purple,
                            borderWidth: 2,
                            borderRadius: 6,
                            maxBarThickness: 36,
                            yAxisID: 'y',
                            order: 2,
                        },
                        {
                            label: 'Nilai Received (Rp)',
                            type: 'line',
                            data: data.monthly_pos.map(m => m.value),
                            borderColor: COLORS.info,
                            backgroundColor: COLORS.info + '10',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            borderWidth: 2,
                            yAxisID: 'y1',
                            order: 1,
                        },
                    ],
                },
                options: opts,
            });
        }

        // 2c. Status Distribution — Doughnut
        const statusCtx = document.getElementById('chartStatusDist');
        if (statusCtx && data.status_dist) {
            if (charts.statusDist) charts.statusDist.destroy();
            const labels = Object.keys(data.status_dist);
            const values = Object.values(data.status_dist);
            const colorPalette = [
                COLORS.warning, COLORS.info, COLORS.orange,
                COLORS.danger, COLORS.purple, COLORS.success, COLORS.muted,
            ];
            const colors = labels.map((_, i) => colorPalette[i % colorPalette.length]);

            charts.statusDist = new Chart(statusCtx, {
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

        // 2d. Top 5 Units — Horizontal Bar
        const unitsCtx = document.getElementById('chartTopUnits');
        if (unitsCtx && data.top_units) {
            if (charts.topUnits) charts.topUnits.destroy();
            const items = Array.isArray(data.top_units) ? data.top_units : Object.values(data.top_units);
            const opts = chartBaseOpts(false);
            opts.indexAxis = 'y';
            opts.scales.x.grid = { color: COLORS.grid };
            opts.scales.y.grid = { display: false };

            charts.topUnits = new Chart(unitsCtx, {
                type: 'bar',
                data: {
                    labels: items.map(i => i.name.length > 25 ? i.name.substring(0, 25) + '...' : i.name),
                    datasets: [{
                        label: 'Jumlah Permintaan',
                        data: items.map(i => i.total),
                        backgroundColor: COLORS.indigo + '22',
                        borderColor: COLORS.indigo,
                        borderWidth: 2,
                        borderRadius: 6,
                        maxBarThickness: 20,
                    }],
                },
                options: opts,
            });
        }
    }

    function loadCharts() {
        ajax(buildUrl(API.charts)).then(renderCharts).fail(() => {
            console.error('Gagal memuat chart dashboard');
        });
    }

    // ── Initial load ────────────────────────────────────────────
    loadStats();
    loadCharts();

    // ── Filter handler ──────────────────────────────────────────
    $('#btnApplyFilter').on('click', function () {
        loadStats();
        loadCharts();
    });

});
</script>
