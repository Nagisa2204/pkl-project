<div class="contents">
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-content">
            Dashboard Admin
        </h1>
    </div>

    <div class="flex justify-between items-center mb-4">
        <select wire:model.live="period" class="ui-field w-full sm:w-48">
            <option value="7days">
                7 Hari
            </option>
            <option value="1month">
                1 Bulan
            </option>

            <option value="1year">
                1 Tahun
            </option>
        </select>
    </div>

    <div class="ui-card mb-4 grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="w-full p-4 rounded-lg border border-default shadow-sm">
            <div>
                <p class="text-sm font-semibold text-content uppercase tracking-wider">
                    Total Pendapatan
                </p>
                <h3 class="text-2xl font-extrabold text-content mt-1">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h3>
            </div>
        </div>

        <div class="w-full p-4 rounded-lg border border-default shadow-sm">
            <div>
                <p class="text-sm font-semibold text-content uppercase tracking-wider">
                    Total Transaksi
                </p>
                <h3 class="text-2xl font-extrabold text-content mt-1">
                    {{ number_format($totalTransactions) }}
                </h3>
            </div>
        </div>

        <div class="w-full p-4 rounded-lg border border-default shadow-sm">
            <div>
                <p class="text-sm font-semibold text-content uppercase tracking-wider">
                    Produk Terjual
                </p>
                <h3 class="text-2xl font-extrabold text-content mt-1">
                    {{ number_format($totalItemsSold) }}

                    <span class="text-xs font-normal text-muted">
                        item
                    </span>
                </h3>
            </div>
        </div>

        <div class="w-full p-4 rounded-lg border border-default shadow-sm">
            <div>
                <p class="text-sm font-semibold text-content uppercase tracking-wider">
                    Total Pelanggan
                </p>
                <h3 class="text-2xl font-extrabold text-content mt-1">
                    {{ number_format($totalCustomers) }}
                </h3>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="ui-card p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-content">
                    Distribusi Penjualan
                </h3>
                <p class="text-sm text-muted">
                    Ringkasan data penjualan.
                </p>
            </div>

            <div class="h-80 flex items-center justify-center" wire:ignore>
                <canvas id="salesPieChart"></canvas>
            </div>
        </div>

        <div class="ui-card p-6">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-content">
                        Grafik Pendapatan
                    </h3>
                </div>
                <span class="rounded-full bg-warning-soft px-3 py-1 text-xs font-semibold text-warning">
                    Waktu Nyata
                </span>
            </div>
            <div class="h-80" wire:ignore>
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
</div>

@script
<script>
    let lineChart, pieChart;

    const themeColor = (token, alpha = 1) => {
        const value = getComputedStyle(document.documentElement).getPropertyValue(token).trim();
        return `rgb(${value} / ${alpha})`;
    };

    function initCharts() {
        const lineCtx = document.getElementById('salesChart').getContext('2d');
        lineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: $wire.chartLabels, 
                datasets: [{
                    label: 'Pendapatan',
                    data: $wire.chartValues,
                    borderColor: themeColor('--primary'),
                    backgroundColor: themeColor('--primary', .08),
                    fill: { target: 'origin' },
                    tension: .45,
                    borderWidth: 3,
                    pointBackgroundColor: themeColor('--primary'),
                    pointBorderColor: themeColor('--surface'),
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        },
                        grid: { color: themeColor('--border') }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        const pieCtx = document.getElementById('salesPieChart').getContext('2d');
        pieChart = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Produk Terjual', 'Pelanggan'],
                datasets: [{
                    data: [$wire.totalItemsSold, $wire.totalCustomers], 
                    backgroundColor: [themeColor('--success'), themeColor('--info')],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 20 }
                    }
                }
            }
        });
    }

    initCharts();

    $wire.on('chart-updated', () => {
        lineChart.data.labels = $wire.chartLabels;
        lineChart.data.datasets[0].data = $wire.chartValues;
        lineChart.update();
    });
</script>
@endscript
</div>
