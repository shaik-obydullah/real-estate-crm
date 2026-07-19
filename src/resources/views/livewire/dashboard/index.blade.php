

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-500">Welcome back! Here's what's happening today.</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Customers</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalCustomers) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Active Leads</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($activeLeads) }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-funnel-dollar text-amber-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pipeline Value</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">${{ number_format($pipelineValue, 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending Tasks</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($pendingTasks) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-square text-red-600 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Activities --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Recent Activities</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentActivities as $activity)
                <div class="px-6 py-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                        {{ $activity->type === 'call' ? 'bg-blue-100 text-blue-600' : '' }}
                        {{ $activity->type === 'email' ? 'bg-green-100 text-green-600' : '' }}
                        {{ $activity->type === 'meeting' ? 'bg-purple-100 text-purple-600' : '' }}
                        {{ $activity->type === 'note' ? 'bg-amber-100 text-amber-600' : '' }}
                        {{ !in_array($activity->type, ['call','email','meeting','note']) ? 'bg-gray-100 text-gray-600' : '' }}">
                        <i class="fas fa-{{ $activity->type === 'call' ? 'phone' : ($activity->type === 'email' ? 'envelope' : ($activity->type === 'meeting' ? 'users' : 'sticky-note')) }} text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $activity->title }}</p>
                        <p class="text-xs text-gray-500">{{ $activity->date?->format('M d, Y') }} {{ $activity->time }}</p>
                    </div>
                    <span class="text-xs text-gray-400 capitalize">{{ $activity->type }}</span>
                </div>
                @empty
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-clipboard-list text-3xl text-gray-300 mb-3"></i>
                    <p class="text-sm text-gray-500">No recent activities</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Sidebar Cards --}}
        <div class="space-y-6">
            {{-- Recent Leads --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Leads</h2>
                    <a href="{{ route('leads.index') }}" wire:navigate class="text-sm text-blue-600 hover:text-blue-700">View All</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($recentLeads as $lead)
                    <div class="px-6 py-3">
                        <p class="text-sm font-medium text-gray-900">{{ $lead->title }}</p>
                        <p class="text-xs text-gray-500">{{ $lead->company_name }} &middot; ${{ number_format($lead->value) }}</p>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center">
                        <p class="text-sm text-gray-500">No leads yet</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Customers --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Customers</h2>
                    <a href="{{ route('customers.index') }}" wire:navigate class="text-sm text-blue-600 hover:text-blue-700">View All</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($recentCustomers as $customer)
                    <div class="px-6 py-3">
                        <p class="text-sm font-medium text-gray-900">{{ $customer->name }}</p>
                        <p class="text-xs text-gray-500">{{ $customer->email }} &middot; {{ ucfirst($customer->status) }}</p>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center">
                        <p class="text-sm text-gray-500">No customers yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
