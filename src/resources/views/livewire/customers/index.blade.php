

<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
            <p class="text-sm text-gray-500">{{ $totalCustomers }} total customers</p>
        </div>
        <a href="{{ route('customers.create') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
            <i class="fas fa-plus"></i> Add Customer
        </a>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Table Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        {{-- Search & Filters --}}
        <div class="p-4 border-b border-gray-100 space-y-3">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name, email, or phone..." class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 items-center">
                <select wire:model.live="statusFilter" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="archived">Archived</option>
                </select>
                <select wire:model.live="typeFilter" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">All Types</option>
                    <option value="individual">Individual</option>
                    <option value="company">Company</option>
                </select>
                <select wire:model.live="accountManagerFilter" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">All Account Managers</option>
                    @foreach($this->accountManagers as $manager)
                    <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                    @endforeach
                </select>
                <button class="inline-flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-sliders-h"></i> More Filters
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-6 py-3 w-10">
                            <input type="checkbox" wire:model.live="selected" value="{{ $customer->id ?? '' }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" x-on:click="$wire.toggleSelectAll()">
                        </th>
                        <th class="px-6 py-3 font-medium text-gray-500 cursor-pointer hover:text-gray-700" wire:click="sortBy('name')">
                            <div class="flex items-center gap-1">
                                Customer
                                @if($sortBy === 'name')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @else
                                <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">Type</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Email</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">Phone</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden xl:table-cell">Account Manager</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden xl:table-cell">Credit Limit</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden 2xl:table-cell cursor-pointer hover:text-gray-700" wire:click="sortBy('created_at')">
                            <div class="flex items-center gap-1">
                                Created
                                @if($sortBy === 'created_at')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @else
                                <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 font-medium text-gray-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($customers as $customer)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <input type="checkbox" wire:model.live="selected" value="{{ $customer->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-semibold text-blue-700">{{ substr($customer->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $customer->name }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $customer->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $customer->type === 'company' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                <i class="fas {{ $customer->type === 'company' ? 'fa-building' : 'fa-user' }} mr-1"></i>
                                {{ ucfirst($customer->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden md:table-cell">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-envelope text-gray-400 text-xs"></i>
                                {{ $customer->email ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden lg:table-cell">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-phone text-gray-400 text-xs"></i>
                                {{ $customer->phone ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($customer->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Active
                            </span>
                            @elseif($customer->status === 'inactive')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span> Inactive
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span> Archived
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden xl:table-cell">
                            {{ $customer->accountManager->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden xl:table-cell font-medium">
                            ${{ number_format($customer->credit_limit, 2) }}
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden 2xl:table-cell text-xs">
                            {{ $customer->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('customers.edit', $customer) }}" wire:navigate class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <button wire:click="confirmDelete({{ $customer->id }})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-users text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-900 mb-1">No customers found</p>
                                <p class="text-sm text-gray-500 mb-4">Try adjusting your search or filters</p>
                                <a href="{{ route('customers.create') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                                    <i class="fas fa-plus"></i> Add Customer
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers
            </p>
            {{ $customers->links() }}
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
@if ($deleteId)
<div class="fixed inset-0 z-50 flex items-center justify-center" x-data x-init="$nextTick(() => $refs.confirmBtn.focus())" @keydown.escape.window="$wire.$set('deleteId', null)">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="$wire.$set('deleteId', null)"></div>
    <div class="relative bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4 z-10 transform transition-all">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Delete Customer</h3>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete this customer? All associated data will also be removed.</p>
        <div class="flex items-center justify-end gap-3">
            <button wire:click="$set('deleteId', null)" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            <button x-ref="confirmBtn" wire:click="deleteCustomer" class="px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition shadow-sm">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
        </div>
    </div>
</div>
@endif
