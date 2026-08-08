<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">
            Dashboard Admin
        </h1>
    </div>

    <div class="flex justify-between items-center mb-4">
        <select wire:model.live="period" class="rounded-lg border-gray-300 text-sm">
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

    <div class="flex items-center justify-between grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-4 bg-white rounded-lg p-4 border border-gray-100 shadow-sm">
        <div class="w-full p-4 rounded-lg border border-gray-100 shadow-sm">
            <div>
                <p class="text-sm font-semibold text-gray-900 uppercase tracking-wider">
                    Total Pendapatan
                </p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-1">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h3>
            </div>
        </div>

        <div class="w-full p-4 rounded-lg border border-gray-100 shadow-sm">
            <div>
                <p class="text-sm font-semibold text-gray-900 uppercase tracking-wider">
                    Total Transaksi
                </p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-1">
                    {{ number_format($totalTransactions) }}
                </h3>
            </div>
        </div>

        <div class="w-full p-4 rounded-lg border border-gray-100 shadow-sm">
            <div>
                <p class="text-sm font-semibold text-gray-900 uppercase tracking-wider">
                    MAP Terjual
                </p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-1">
                    {{ number_format($totalItemsSold) }}

                    <span class="text-xs font-normal text-gray-500">
                        LembarS
                    </span>
                </h3>
            </div>
        </div>

        <div class="w-full p-4 rounded-lg border border-gray-100 shadow-sm">
            <div>
                <p class="text-sm font-semibold text-gray-900 uppercase tracking-wider">
                    Total Pelanggan
                </p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-1">
                    {{ number_format($totalCustomers) }}
                </h3>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    Distribusi Penjualan
                </h3>
                <p class="text-sm text-gray-500">
                    Ringkasan data penjualan.
                </p>
            </div>

            <div class="h-80 flex items-center justify-center" wire:ignore>
                <canvas id="salesPieChart"></canvas>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        Grafik Pendapatan
                    </h3>
                </div>
                <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-600">
                    Real-time
                </span>
            </div>
            <div class="h-80" wire:ignore>
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@script
<script>
    let lineChart, pieChart;

    function initCharts() {
        const lineCtx = document.getElementById('salesChart').getContext('2d');
        lineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: $wire.chartLabels, 
                datasets: [{
                    label: 'Pendapatan',
                    data: $wire.chartValues,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249,115,22,.08)',
                    fill: { target: 'origin' },
                    tension: .45,
                    borderWidth: 3,
                    pointBackgroundColor: '#f97316',
                    pointBorderColor: '#ffffff',
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
                        grid: { color: '#f3f4f6' }
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
                labels: ['MAP Terjual', 'Pelanggan'],
                datasets: [{
                    data: [$wire.totalItemsSold, $wire.totalCustomers], 
                    backgroundColor: ['#10b981', '#8b5cf6'],
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