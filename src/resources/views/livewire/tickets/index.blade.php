

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tickets</h1>
            <p class="text-sm text-gray-500">Support ticket management</p>
        </div>
        <button wire:click="openCreate" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
            <i class="fas fa-plus"></i> New Ticket
        </button>
    </div>

    @if (session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 border-b border-gray-100 space-y-3">
            <div class="flex flex-col lg:flex-row gap-3">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by subject or ticket #..." class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
                <select wire:model.live="statusFilter" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">All Statuses</option>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="waiting">Waiting</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
                <select wire:model.live="priorityFilter" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">All Priorities</option>
                    <option value="urgent">Urgent</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
                <select wire:model.live="assignedFilter" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">All Assignees</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-6 py-3 font-medium text-gray-500">Ticket</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Customer</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Priority</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">Assigned To</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">Created</th>
                        <th class="px-6 py-3 font-medium text-gray-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $ticket->subject }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $ticket->ticket_number }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden md:table-cell">{{ $ticket->customer->name ?? '-' }}</td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize {{ $this->getPriorityColor($ticket->priority) }}">{{ $ticket->priority }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize {{ $this->getStatusColor($ticket->status) }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden lg:table-cell">{{ $ticket->assignedTo->name ?? 'Unassigned' }}</td>
                        <td class="px-6 py-4 text-gray-500 hidden lg:table-cell text-xs whitespace-nowrap">{{ $ticket->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="openEdit({{ $ticket->id }})" class="p-2 text-gray-400 hover:text-amber-600 rounded-lg hover:bg-amber-50 transition" title="Edit">
                                    <i class="fas fa-pen text-sm"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $ticket->id }})" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" title="Delete">
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
                                    <i class="fas fa-life-ring text-gray-400 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">No tickets found</p>
                                    <p class="text-xs text-gray-500">Create your first support ticket to get started</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $tickets->links() }}
        </div>
    </div>

    @if($deleteId)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data @keydown.escape.window="$wire.set('deleteId', null)">
        <div class="fixed inset-0 bg-black/50 transition-opacity" @click="$wire.set('deleteId', null)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Delete Ticket</h3>
                </div>
                <p class="text-sm text-gray-500">Are you sure you want to delete this ticket? This action cannot be undone.</p>
                <div class="flex justify-end gap-3">
                    <button @click="$wire.set('deleteId', null)" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <button wire:click="deleteTicket({{ $deleteId }})" class="px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data>
        <div class="fixed inset-0 bg-black/50 transition-opacity" @click="$wire.call('closeModal')"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $editId ? 'Edit Ticket' : 'New Ticket' }}</h3>
                    <button @click="$wire.call('closeModal')" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form wire:submit="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject <span class="text-red-500">*</span></label>
                        <input wire:model="formSubject" type="text" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('formSubject') border-red-500 @enderror" placeholder="Brief summary of the issue">
                        @error('formSubject') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea wire:model="formDescription" rows="3" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none @error('formDescription') border-red-500 @enderror" placeholder="Detailed description of the ticket"></textarea>
                        @error('formDescription') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority <span class="text-red-500">*</span></label>
                            <select wire:model="formPriority" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white transition @error('formPriority') border-red-500 @enderror">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                            @error('formPriority') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                            <select wire:model="formStatus" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white transition @error('formStatus') border-red-500 @enderror">
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="waiting">Waiting</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                            @error('formStatus') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                            <select wire:model="formCustomer" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white transition">
                                <option value="0">Select customer...</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                            <select wire:model="formAssignedTo" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white transition">
                                <option value="0">Unassigned</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="$wire.call('closeModal')" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition shadow-sm">
                            <span wire:loading.remove wire:target="save"><i class="fas fa-save mr-1"></i> {{ $editId ? 'Update Ticket' : 'Create Ticket' }}</span>
                            <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin mr-1"></i> Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
