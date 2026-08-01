{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const API = {
        stats:    '{{ route("api.dashboard.sarpras.stats") }}',
        charts:   '{{ route("api.dashboard.sarpras.charts") }}',
        activity: '{{ route("api.dashboard.sarpras.activity") }}',
    };

    const COLORS = {
        primary : '#4f46e5',
        success : '#22c55e',
        warning : '#eab308',
        danger  : '#ef4444',
        info    : '#0ea5e9',
        purple  : '#8b5cf6',
        orange  : '#f97316',
        muted   : '#94a3b8',
        grid    : 'rgba(0,0,0,0.05)',
    };

    // ── Chart instances (untuk di-destroy saat re-render) ─────
    const charts = {
        monthlyRequests: null,
        stockStatus     : null,
        stockMovements  : null,
        topItems        : null,
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

    function chartBaseOpts() {
        return {
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
    }

    // ── Live Clock ───────────────────────────────────────────────
    function updateClock() {
        const now = new Date();
        const opts = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
        $('#clockDisplay').text(now.toLocaleTimeString('id-ID', opts) + ' WIB');
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── 1. Load Stats ───────────────────────────────────────────
    function loadStats() {
        ajax(API.stats).then(data => {
            const keys = [
                'pending_requests', 'ready_requests', 'low_stock_items',
                'empty_stock_items', 'draft_pos', 'active_pos',
                'open_opname', 'total_items',
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

        // 2a. Monthly Requests — Stacked Bar Chart
        const monthlyCtx = document.getElementById('chartMonthlyRequests');
        if (monthlyCtx) {
            if (charts.monthlyRequests) charts.monthlyRequests.destroy();
            const opts = chartBaseOpts();
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

            charts.monthlyRequests = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: data.monthly_requests.map(m => m.label),
                    datasets: [
                        {
                            label: 'Total',
                            data: data.monthly_requests.map(m => m.total),
                            backgroundColor: COLORS.primary + '22',
                            borderColor: COLORS.primary,
                            borderWidth: 2,
                            borderRadius: 6,
                            maxBarThickness: 36,
                        },
                        {
                            label: 'Selesai',
                            data: data.monthly_requests.map(m => m.completed),
                            backgroundColor: COLORS.success + '22',
                            borderColor: COLORS.success,
                            borderWidth: 2,
                            borderRadius: 6,
                            maxBarThickness: 36,
                        },
                        {
                            label: 'Ditolak',
                            data: data.monthly_requests.map(m => m.rejected),
                            backgroundColor: COLORS.danger + '22',
                            borderColor: COLORS.danger,
                            borderWidth: 2,
                            borderRadius: 6,
                            maxBarThickness: 36,
                        },
                    ],
                },
                options: opts,
            });
        }

        // 2b. Stock Status — Doughnut
        const stockCtx = document.getElementById('chartStockStatus');
        if (stockCtx && data.stock_status) {
            if (charts.stockStatus) charts.stockStatus.destroy();
            const labels = Object.keys(data.stock_status);
            const values = Object.values(data.stock_status);
            const colorMap = { 'Normal': COLORS.success, 'Rendah': COLORS.warning, 'Habis': COLORS.danger };
            const colors = labels.map(l => colorMap[l] || COLORS.muted);

            charts.stockStatus = new Chart(stockCtx, {
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
            $('#stockLegend').html(legendHtml);
        }

        // 2c. Stock Movements — Line chart
        const movCtx = document.getElementById('chartStockMovements');
        if (movCtx && data.stock_movements) {
            if (charts.stockMovements) charts.stockMovements.destroy();
            const opts = chartBaseOpts();
            opts.plugins.legend = {
                display: true, position: 'top', align: 'end',
                labels: {
                    usePointStyle: true, pointStyle: 'circle', boxWidth: 8, padding: 16,
                    font: { family: "'Plus Jakarta Sans'", size: 11.5 }, color: COLORS.muted,
                },
            };

            charts.stockMovements = new Chart(movCtx, {
                type: 'line',
                data: {
                    labels: data.stock_movements.map(m => m.label),
                    datasets: [
                        {
                            label: 'Masuk',
                            data: data.stock_movements.map(m => m.in),
                            borderColor: COLORS.success,
                            backgroundColor: COLORS.success + '15',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            borderWidth: 2,
                        },
                        {
                            label: 'Keluar',
                            data: data.stock_movements.map(m => m.out),
                            borderColor: COLORS.danger,
                            backgroundColor: COLORS.danger + '15',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            borderWidth: 2,
                        },
                    ],
                },
                options: opts,
            });
        }

        // 2d. Top 5 Items — Horizontal Bar
        const topCtx = document.getElementById('chartTopItems');
        if (topCtx && data.top_items) {
            if (charts.topItems) charts.topItems.destroy();
            const items = Array.isArray(data.top_items) ? data.top_items : Object.values(data.top_items);
            const opts = chartBaseOpts();
            opts.indexAxis = 'y';
            opts.scales.x.grid = { color: COLORS.grid };
            opts.scales.y.grid = { display: false };

            charts.topItems = new Chart(topCtx, {
                type: 'bar',
                data: {
                    labels: items.map(i => i.name.length > 30 ? i.name.substring(0, 30) + '...' : i.name),
                    datasets: [{
                        label: 'Total Diminta',
                        data: items.map(i => i.total_qty),
                        backgroundColor: COLORS.info + '22',
                        borderColor: COLORS.info,
                        borderWidth: 2,
                        borderRadius: 6,
                        maxBarThickness: 24,
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

    // ── 3. Load Activity ────────────────────────────────────────
    function loadActivity() {
        ajax(API.activity).then(data => {

            // 3a. Pending Requests List
            const $pr = $('#pendingRequestsList');
            if (!data.pending_requests.length) {
                $pr.html('<div class="text-center py-4 text-muted"><i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>Tidak ada permintaan pending. 🎉</div>');
            } else {
                let html = '<div class="list-group list-group-flush">';
                data.pending_requests.forEach(r => {
                    const pBadge = r.priority === 'urgent'
                        ? '<span class="badge bg-danger bg-opacity-10 text-danger" style="font-size:10px">Urgent</span>'
                        : '';
                    html += `
                    <a href="${r.url}" class="list-group-item list-group-item-action border-0 px-0 py-2" style="background:transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold" style="font-size:13px;color:var(--text-primary)">${r.request_number} ${pBadge}</div>
                                <div class="text-muted" style="font-size:12px">${r.unit} · ${r.requester} · ${r.items_count} barang</div>
                            </div>
                            <span class="text-muted" style="font-size:11.5px">${r.request_date}</span>
                        </div>
                    </a>`;
                });
                html += '</div>';
                $pr.html(html);
            }

            // 3b. Low Stock Items
            const $ls = $('#lowStockList');
            if (!data.low_stock_items.length) {
                $ls.html('<div class="text-center py-4 text-muted"><i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>Semua stok dalam kondisi aman.</div>');
            } else {
                let html = '<div class="list-group list-group-flush">';
                data.low_stock_items.forEach(i => {
                    html += `
                    <a href="${i.url}" class="list-group-item list-group-item-action border-0 px-0 py-2" style="background:transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold" style="font-size:13px;color:var(--text-primary)">${i.name}</div>
                                <div class="text-muted" style="font-size:12px">${i.code} · ${i.category}</div>
                            </div>
                            <span class="badge bg-warning bg-opacity-10 text-warning fw-bold" style="font-size:12px">
                                ${i.total_stock} ${i.uom}
                            </span>
                        </div>
                    </a>`;
                });
                html += '</div>';
                $ls.html(html);
            }

            // 3c. Recent Movements
            const $rm = $('#recentMovementsList');
            if (!data.recent_movements.length) {
                $rm.html('<div class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada pergerakan stok.</div>');
            } else {
                let html = '<div class="table-responsive"><table class="table table-hover align-middle mb-0" style="font-size:13px">';
                html += `<thead><tr style="color:var(--text-muted);font-size:11px;text-transform:uppercase;letter-spacing:.5px">
                    <th class="border-0 pb-2">Barang</th>
                    <th class="border-0 pb-2">Tipe</th>
                    <th class="border-0 pb-2">Jumlah</th>
                    <th class="border-0 pb-2">Stok Setelah</th>
                    <th class="border-0 pb-2">Oleh</th>
                    <th class="border-0 pb-2">Waktu</th>
                </tr></thead><tbody>`;

                data.recent_movements.forEach(m => {
                    const isIn = m.type === 'in';
                    const typeLabel = isIn
                        ? '<span class="badge bg-success bg-opacity-10 text-success" style="font-size:11px"><i class="bi bi-arrow-down-short"></i> Masuk</span>'
                        : '<span class="badge bg-danger bg-opacity-10 text-danger" style="font-size:11px"><i class="bi bi-arrow-up-short"></i> Keluar</span>';

                    html += `<tr>
                        <td class="fw-semibold" style="color:var(--text-primary)">${m.item}</td>
                        <td>${typeLabel}</td>
                        <td style="color:var(--text-primary)">${Math.abs(m.quantity)}</td>
                        <td style="color:var(--text-secondary)">${m.stock_after ?? '—'}</td>
                        <td style="color:var(--text-secondary)">${m.performer}</td>
                        <td style="color:var(--text-muted);font-size:12px">${m.created_at}</td>
                    </tr>`;
                });

                html += '</tbody></table></div>';
                $rm.html(html);
            }
        }).fail(() => {
            ['pendingRequestsList','lowStockList','recentMovementsList'].forEach(id => {
                $(`#${id}`).html('<div class="text-center py-3 text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Gagal memuat data.</div>');
            });
        });
    }

    // ── Initial load ────────────────────────────────────────────
    loadStats();
    loadCharts();
    loadActivity();

    // ── Filter handler ──────────────────────────────────────────
    $('#btnApplyFilter').on('click', function () {
        loadStats();
        loadCharts();
    });

});
</script>
