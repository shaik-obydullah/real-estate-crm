
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
            <p class="text-sm text-gray-500">Business intelligence and analytics</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button wire:click="exportData('csv')" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-file-csv text-green-600"></i> CSV
            </button>
            <button wire:click="exportData('excel')" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-file-excel text-green-600"></i> Excel
            </button>
            <button wire:click="exportData('pdf')" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-file-pdf text-red-600"></i> PDF
            </button>
        </div>
    </div>

    @if (session()->has('success') || session()->has('info'))
        <div class="{{ session()->has('success') ? 'bg-green-50 border-green-200 text-green-700' : 'bg-blue-50 border-blue-200 text-blue-700' }} border px-4 py-3 rounded-lg text-sm">
            {{ session('success') ?? session('info') }}
        </div>
    @endif

    <!-- Date Range Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <h3 class="text-sm font-medium text-gray-700">Date Range:</h3>
            <div class="flex gap-3">
                <input wire:model.live="dateFrom" type="date" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                <span class="text-gray-400 py-2">to</span>
                <input wire:model.live="dateTo" type="date" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">${{ number_format($data['total_revenue'], 2) }}</div>
            <div class="text-xs text-gray-500 mt-1">${{ number_format($data['revenue_in_period'], 2) }} this period</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Customers</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($data['total_customers']) }}</div>
            <div class="text-xs text-gray-500 mt-1">+{{ $data['new_customers_period'] }} this period</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Conversion Rate</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $data['conversion_rate'] }}%</div>
            <div class="text-xs text-gray-500 mt-1">{{ $data['converted_leads'] }} of {{ $data['leads_in_period'] }} leads</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pipeline Value</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">${{ number_format($data['pipeline_value'], 2) }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $data['open_opportunities'] }} open opportunities</div>
        </div>
    </div>

    <!-- Report Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $reports = [
                ['title' => 'Revenue Analysis', 'icon' => 'fa-dollar-sign', 'color' => 'blue', 'desc' => 'Detailed revenue breakdown and trends', 'value' => '$' . number_format($data['revenue_in_period'], 2)],
                ['title' => 'Lead Conversion', 'icon' => 'fa-funnel-dollar', 'color' => 'green', 'desc' => 'Lead sources and conversion metrics', 'value' => $data['conversion_rate'] . '%'],
                ['title' => 'Customer Analytics', 'icon' => 'fa-users', 'color' => 'purple', 'desc' => 'Customer demographics and segments', 'value' => number_format($data['total_customers'])],
                ['title' => 'Sales Pipeline', 'icon' => 'fa-chart-line', 'color' => 'yellow', 'desc' => 'Pipeline stages and deal flow', 'value' => $data['open_opportunities'] . ' deals'],
                ['title' => 'Activity Summary', 'icon' => 'fa-clipboard-list', 'color' => 'indigo', 'desc' => 'Tasks, meetings, and calls overview', 'value' => '-'],
                ['title' => 'Product Performance', 'icon' => 'fa-box', 'color' => 'pink', 'desc' => 'Product sales and performance metrics', 'value' => '-'],
                ['title' => 'Team Performance', 'icon' => 'fa-trophy', 'color' => 'orange', 'desc' => 'Individual and team KPIs', 'value' => $data['total_users'] . ' users'],
                ['title' => 'Forecast', 'icon' => 'fa-crystal-ball', 'color' => 'teal', 'desc' => 'Revenue and growth projections', 'value' => '-'],
            ];
        @endphp

        @foreach($reports as $report)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition cursor-pointer">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-{{ $report['color'] }}-100 flex items-center justify-center">
                        <i class="fas {{ $report['icon'] }} text-{{ $report['color'] }}-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ $report['title'] }}</h3>
                        <p class="text-xs text-gray-500">{{ $report['desc'] }}</p>
                    </div>
                </div>
                <div class="mt-2 pt-3 border-t border-gray-100">
                    <div class="text-lg font-bold text-gray-900">{{ $report['value'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Revenue Over Time</h3>
            <div class="h-64 bg-gray-50 rounded-lg relative">
                <canvas id="revenueChart" data-chart-labels='{{ json_encode($data['revenue_chart_labels']) }}' data-chart-values='{{ json_encode($data['revenue_chart_values']) }}'></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Lead Sources</h3>
            <div class="h-64 bg-gray-50 rounded-lg relative">
                <canvas id="leadSourcesChart" data-chart-labels='{{ json_encode($data['lead_sources_chart_labels']) }}' data-chart-values='{{ json_encode($data['lead_sources_chart_values']) }}'></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.renderReportCharts = window.renderReportCharts || (function () {
        let revenueChart = null;
        let leadSourcesChart = null;

        return function () {
            if (typeof Chart === 'undefined') {
                return;
            }

            const revenueEl = document.getElementById('revenueChart');
            const leadEl = document.getElementById('leadSourcesChart');

            if (revenueChart) revenueChart.destroy();
            if (leadSourcesChart) leadSourcesChart.destroy();
            revenueChart = null;
            leadSourcesChart = null;

            const readChartData = (el) => ({
                labels: el && el.dataset.chartLabels ? JSON.parse(el.dataset.chartLabels) : [],
                values: el && el.dataset.chartValues ? JSON.parse(el.dataset.chartValues) : [],
            });

            const revenue = readChartData(revenueEl);
            const leadSources = readChartData(leadEl);

            if (revenueEl) {
                revenueChart = new Chart(revenueEl, {
                    type: 'line',
                    data: {
                        labels: revenue.labels,
                        datasets: [{
                            label: 'Revenue',
                            data: revenue.values,
                            borderColor: 'rgb(37, 99, 235)',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: 'rgb(37, 99, 235)',
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (value) => '$' + Number(value).toLocaleString(),
                                },
                            },
                        },
                    },
                });
            }

            if (leadEl) {
                leadSourcesChart = new Chart(leadEl, {
                    type: 'doughnut',
                    data: {
                        labels: leadSources.labels,
                        datasets: [{
                            data: leadSources.values,
                            backgroundColor: [
                                'rgba(37, 99, 235, 0.8)',
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(139, 92, 246, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(236, 72, 153, 0.8)',
                                'rgba(20, 184, 166, 0.8)',
                            ],
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 12,
                                },
                            },
                        },
                    },
                });
            }
        };
    })();

    if (!window.__reportChartsHooksRegistered) {
        window.__reportChartsHooksRegistered = true;

        document.addEventListener('livewire:init', () => {
            window.renderReportCharts();

            Livewire.hook('morph.added', ({ el }) => {
                if (el.id === 'revenueChart' || el.id === 'leadSourcesChart') {
                    window.renderReportCharts();
                }
            });

            Livewire.hook('morph.updated', ({ el }) => {
                if (el.id === 'revenueChart' || el.id === 'leadSourcesChart') {
                    window.renderReportCharts();
                }
            });

            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => window.renderReportCharts());
            });
        });

        document.addEventListener('livewire:navigated', () => {
            window.renderReportCharts();
        });
    }
</script>
@endpush
