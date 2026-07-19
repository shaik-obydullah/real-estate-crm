

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quotations</h1>
            <p class="text-sm text-gray-500">{{ $totalQuotations }} total quotations</p>
        </div>
        <a href="{{ route('quotations.create') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
            <i class="fas fa-plus"></i> New Quotation
        </a>
    </div>

    @if (session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 border-b border-gray-100 space-y-3">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by quote # or customer..." class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 items-center">
                <select wire:model.live="statusFilter" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="sent">Sent</option>
                    <option value="accepted">Accepted</option>
                    <option value="rejected">Rejected</option>
                    <option value="expired">Expired</option>
                </select>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-500">From</label>
                    <input wire:model.live="dateFrom" type="date" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-500">To</label>
                    <input wire:model.live="dateTo" type="date" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-6 py-3 font-medium text-gray-500 cursor-pointer hover:text-gray-700" wire:click="sortBy('quote_number')">
                            <div class="flex items-center gap-1">Quote # @if($sortBy === 'quote_number')<i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>@else<i class="fas fa-sort text-gray-300"></i>@endif</div>
                        </th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Customer</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell cursor-pointer hover:text-gray-700" wire:click="sortBy('total')">
                            <div class="flex items-center gap-1">Amount @if($sortBy === 'total')<i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>@else<i class="fas fa-sort text-gray-300"></i>@endif</div>
                        </th>
                        <th class="px-6 py-3 font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">Valid Until</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">Created</th>
                        <th class="px-6 py-3 font-medium text-gray-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($quotations as $quotation)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $quotation->quote_number }}</td>
                        <td class="px-6 py-4 text-gray-500 hidden md:table-cell">{{ $quotation->customer->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-500 hidden md:table-cell">${{ number_format($quotation->total, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($quotation->status) }}">{{ ucfirst($quotation->status) }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden lg:table-cell">{{ $quotation->valid_until?->format('M d, Y') ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-500 hidden lg:table-cell">{{ $quotation->created_at?->format('M d, Y') ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1" x-data>
                                <a href="{{ route('quotations.show', $quotation) }}" wire:navigate class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition" title="View">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('quotations.edit', $quotation) }}" wire:navigate class="p-2 text-gray-400 hover:text-amber-600 rounded-lg hover:bg-amber-50 transition" title="Edit">
                                    <i class="fas fa-pen text-sm"></i>
                                </a>
                                <button wire:click="confirmDelete({{ $quotation->id }})" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" title="Delete">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-file-invoice text-gray-400 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">No quotations found</p>
                                    <p class="text-xs text-gray-500">Create your first quotation to get started</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $quotations->links() }}
        </div>
    </div>

    @if($deleteId)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-init="$nextTick(() => $refs.confirmBtn.focus())" @keydown.escape.window="$wire.set('deleteId', null)">
        <div class="fixed inset-0 bg-black/50 transition-opacity" @click="$wire.set('deleteId', null)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Delete Quotation</h3>
                </div>
                <p class="text-sm text-gray-500">Are you sure you want to delete this quotation? This action cannot be undone.</p>
                <div class="flex justify-end gap-3">
                    <button @click="$wire.set('deleteId', null)" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <button wire:click="deleteQuotation" x-ref="confirmBtn" class="px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
