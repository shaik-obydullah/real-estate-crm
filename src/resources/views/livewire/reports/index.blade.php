
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

    <!-- Chart Placeholders -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Revenue Over Time</h3>
            <div id="revenueChart" class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center">
                    <i class="fas fa-chart-area text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-400">Chart.js visualization</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Lead Sources</h3>
            <div id="leadSourcesChart" class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center">
                    <i class="fas fa-chart-pie text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-400">Chart.js visualization</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
