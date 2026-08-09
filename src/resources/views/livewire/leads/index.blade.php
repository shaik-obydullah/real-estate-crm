

<div class="space-y-6" x-data="{ showBulkActions: false }" x-effect="showBulkActions = $wire.selected.length > 0">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Leads</h1>
            <p class="text-sm text-gray-500">{{ $totalLeads }} total leads &middot; Pipeline value: <span class="font-semibold text-blue-600">${{ number_format($this->pipelineValue, 2) }}</span></p>
        </div>
        <div class="flex items-center gap-3">
            {{-- View Toggle --}}
            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                <button wire:click="toggleViewMode('table')" class="px-3 py-1.5 text-sm font-medium rounded-md transition {{ $viewMode === 'table' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-list"></i>
                </button>
                <button wire:click="toggleViewMode('kanban')" class="px-3 py-1.5 text-sm font-medium rounded-md transition {{ $viewMode === 'kanban' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-columns"></i>
                </button>
            </div>
            <a href="{{ route('leads.create') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
                <i class="fas fa-plus"></i> Add Lead
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Table View --}}
    @if($viewMode === 'table')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        {{-- Search & Filters --}}
        <div class="p-4 border-b border-gray-100 space-y-3">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by title, company, or contact..." class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <select wire:model.live="statusFilter" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">All Statuses</option>
                    <option value="new">New</option>
                    <option value="contacted">Contacted</option>
                    <option value="qualified">Qualified</option>
                    <option value="proposal">Proposal Sent</option>
                    <option value="negotiation">Negotiation</option>
                    <option value="converted">Won</option>
                    <option value="lost">Lost</option>
                </select>
                <select wire:model.live="priorityFilter" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">All Priorities</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
                <select wire:model.live="sourceFilter" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">All Sources</option>
                    <option value="website">Website</option>
                    <option value="referral">Referral</option>
                    <option value="social_media">Social Media</option>
                    <option value="advertisement">Advertisement</option>
                    <option value="cold_call">Cold Call</option>
                    <option value="other">Other</option>
                </select>
                <select wire:model.live="salesPersonFilter" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">All Sales People</option>
                    @foreach($this->salesPeople as $person)
                    <option value="{{ $person->id }}">{{ $person->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Bulk Actions --}}
        <div x-show="showBulkActions" x-transition class="px-4 py-3 bg-blue-50 border-b border-blue-100 flex items-center gap-3">
            <span class="text-sm text-blue-700 font-medium" x-text="$wire.selected.length + ' selected'"></span>
            <button wire:click="confirmBulkDelete" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-trash mr-1"></i> Delete Selected
            </button>
            <button class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-download mr-1"></i> Export
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-6 py-3 w-10">
                            <input type="checkbox" wire:model="selectAll" wire:change="toggleSelectAll()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 font-medium text-gray-500 cursor-pointer hover:text-gray-700" wire:click="sortLeadsBy('title')">
                            <div class="flex items-center gap-1">
                                Lead Title
                                @if($sortBy === 'title')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @else
                                <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Company</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">Contact</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">Source</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Value</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">Priority</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden xl:table-cell">Closing Date</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden xl:table-cell">Sales Person</th>
                        <th class="px-6 py-3 font-medium text-gray-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($leads as $lead)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <input type="checkbox" wire:model.live="selected" value="{{ $lead->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $lead->title }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden md:table-cell">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-building text-gray-400 text-xs"></i>
                                {{ $lead->company_name ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden lg:table-cell">{{ $lead->contact_name ?? '-' }}</td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                {{ str_replace('_', ' ', ucfirst($lead->source ?? 'Other')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-900 font-medium hidden md:table-cell">
                            ${{ number_format($lead->value, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                            $statusColors = [
                                'new' => 'bg-blue-100 text-blue-700',
                                'contacted' => 'bg-indigo-100 text-indigo-700',
                                'qualified' => 'bg-yellow-100 text-yellow-700',
                                'proposal' => 'bg-orange-100 text-orange-700',
                                'negotiation' => 'bg-red-100 text-red-700',
                                'converted' => 'bg-green-100 text-green-700',
                                'lost' => 'bg-gray-100 text-gray-600',
                            ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$lead->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($lead->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            @php
                            $priorityColors = [
                                'low' => 'bg-gray-100 text-gray-600',
                                'medium' => 'bg-yellow-100 text-yellow-700',
                                'high' => 'bg-orange-100 text-orange-700',
                                'urgent' => 'bg-red-100 text-red-700',
                            ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityColors[$lead->priority] ?? 'bg-gray-100 text-gray-600' }}">
                                <i class="fas fa-flag mr-1 text-[10px]"></i>
                                {{ ucfirst($lead->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden xl:table-cell text-xs">
                            {{ $lead->expected_closing_date ? $lead->expected_closing_date->format('M d, Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden xl:table-cell">
                            {{ $lead->assignedTo->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('leads.edit', $lead) }}" wire:navigate class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <button wire:click="confirmDelete({{ $lead->id }})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-funnel-dollar text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-900 mb-1">No leads found</p>
                                <p class="text-sm text-gray-500 mb-4">Try adjusting your search or filters</p>
                                <a href="{{ route('leads.create') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                                    <i class="fas fa-plus"></i> Add Lead
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $leads->links() }}
        </div>
    </div>
    @else
    {{-- Kanban View Placeholder --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-columns text-2xl text-gray-400"></i>
        </div>
        <p class="text-sm font-medium text-gray-900">Kanban View</p>
        <p class="text-sm text-gray-500 mt-1">Use the Pipeline board for a kanban-style view of your leads.</p>
        <a href="{{ route('pipeline.index') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 mt-4 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
            <i class="fas fa-filter"></i> Go to Pipeline
        </a>
    </div>
    @endif

    {{-- Delete Confirmation Modal --}}
@if ($deleteId)
<div class="fixed inset-0 z-50 flex items-center justify-center" x-data x-init="$nextTick(() => $refs.confirmBtn.focus())" @keydown.escape.window="$wire.$set('deleteId', null)">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="$wire.$set('deleteId', null)"></div>
    <div class="relative bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4 z-10">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Delete Lead</h3>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 mt-6">
            <button wire:click="$set('deleteId', null)" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            <button x-ref="confirmBtn" wire:click="deleteLead" class="px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition shadow-sm">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
        </div>
    </div>
</div>
@endif

{{-- Bulk Delete Confirmation Modal --}}
@if ($bulkDelete)
<div class="fixed inset-0 z-50 flex items-center justify-center" x-data x-init="$nextTick(() => $refs.bulkConfirmBtn.focus())" @keydown.escape.window="$wire.$set('bulkDelete', false)">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="$wire.$set('bulkDelete', false)"></div>
    <div class="relative bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4 z-10 transform transition-all">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Delete {{ count($selected) }} Leads</h3>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete the {{ count($selected) }} selected leads? All associated data will also be removed.</p>
        <div class="flex items-center justify-end gap-3">
            <button wire:click="$set('bulkDelete', false)" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            <button x-ref="bulkConfirmBtn" wire:click="deleteSelected" class="px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition shadow-sm">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
        </div>
    </div>
</div>
@endif
</div>

