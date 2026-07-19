

<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Contacts</h1>
            <p class="text-sm text-gray-500">{{ $totalContacts }} total contacts</p>
        </div>
        <a href="{{ route('contacts.create') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
            <i class="fas fa-plus"></i> Add Contact
        </a>
    </div>

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
            <div class="flex flex-col sm:flex-row gap-3">
                <select wire:model.live="companyFilter" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="departmentFilter" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept }}">{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-6 py-3 w-10">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" x-on:click="$wire.toggleSelectAll()">
                        </th>
                        <th class="px-6 py-3 font-medium text-gray-500 cursor-pointer hover:text-gray-700" wire:click="sortBy('first_name')">
                            <div class="flex items-center gap-1">
                                Contact
                                @if($sortBy === 'first_name')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @else
                                <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Company</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">Position</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Email</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">Phone</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden xl:table-cell">WhatsApp</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden xl:table-cell">Department</th>
                        <th class="px-6 py-3 font-medium text-gray-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($contacts as $contact)
                    @php
                    $fullName = $contact->first_name . ' ' . $contact->last_name;
                    $colorIndex = abs(crc32($fullName)) % 8;
                    $avatarColors = [
                        'bg-red-100 text-red-700',
                        'bg-blue-100 text-blue-700',
                        'bg-green-100 text-green-700',
                        'bg-yellow-100 text-yellow-700',
                        'bg-purple-100 text-purple-700',
                        'bg-pink-100 text-pink-700',
                        'bg-indigo-100 text-indigo-700',
                        'bg-teal-100 text-teal-700',
                    ];
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <input type="checkbox" wire:model.live="selected" value="{{ $contact->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full {{ $avatarColors[$colorIndex] }} flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-semibold">{{ substr($contact->first_name, 0, 1) }}{{ substr($contact->last_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $fullName }}</p>
                                    @if($contact->is_primary)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-700 mt-0.5">Primary</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden md:table-cell">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-building text-gray-400 text-xs"></i>
                                {{ $contact->customer->name ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden lg:table-cell">
                            {{ $contact->position ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden md:table-cell">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-envelope text-gray-400 text-xs"></i>
                                {{ $contact->email ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden lg:table-cell">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-phone text-gray-400 text-xs"></i>
                                {{ $contact->phone ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden xl:table-cell">
                            @if($contact->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->whatsapp) }}" target="_blank" class="inline-flex items-center gap-1 text-green-600 hover:text-green-700">
                                <i class="fab fa-whatsapp"></i> {{ $contact->whatsapp }}
                            </a>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 hidden xl:table-cell">
                            @if($contact->department)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                {{ $contact->department }}
                            </span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('contacts.edit', $contact) }}" wire:navigate class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <button wire:click="confirmDelete({{ $contact->id }})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-address-book text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-900 mb-1">No contacts found</p>
                                <p class="text-sm text-gray-500 mb-4">Try adjusting your search or filters</p>
                                <a href="{{ route('contacts.create') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                                    <i class="fas fa-plus"></i> Add Contact
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing {{ $contacts->firstItem() ?? 0 }} to {{ $contacts->lastItem() ?? 0 }} of {{ $contacts->total() }} contacts
            </p>
            {{ $contacts->links() }}
        </div>
    </div>
</div>

@if ($deleteId)
<div class="fixed inset-0 z-50 flex items-center justify-center" x-data x-init="$nextTick(() => $refs.confirmBtn.focus())" @keydown.escape.window="$wire.$set('deleteId', null)">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="$wire.$set('deleteId', null)"></div>
    <div class="relative bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4 z-10">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Delete Contact</h3>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 mt-6">
            <button wire:click="$set('deleteId', null)" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            <button x-ref="confirmBtn" wire:click="deleteContact" class="px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition shadow-sm">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
        </div>
    </div>
</div>
@endif
