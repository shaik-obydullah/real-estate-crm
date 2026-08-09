<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-start gap-3">
        <a href="{{ route('followups.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $followup->title }}</h1>
            <p class="text-sm text-gray-500 mt-1 flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                    <i class="{{ $this->getTypeIcon($followup->type) }} mr-1"></i>
                    {{ ucfirst(str_replace('_', ' ', $followup->type)) }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($followup->status) }}">
                    {{ ucfirst($followup->status) }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getPriorityColor($followup->priority) }}">
                    {{ ucfirst($followup->priority) }} priority
                </span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('followups.edit', $followup) }}" wire:navigate class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-sm">
                <i class="fas fa-pen mr-1"></i> Edit
            </a>
            <button wire:click="confirmDelete" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition shadow-sm">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
        </div>
    </div>

    @if (session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Details --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 space-y-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-calendar-check text-blue-500"></i> Follow-up Details
            </h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ \App\Support\AppSettings::formatDate($followup->due_date) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Due Time</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $followup->due_time ? \App\Support\AppSettings::formatTime($followup->due_time) : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Priority</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($followup->priority) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($followup->status) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Reminder</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $followup->reminder_at ? \App\Support\AppSettings::formatDateTime($followup->reminder_at) : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $followup->assignedTo?->name ?? '—' }}</dd>
                </div>
            </dl>

            <hr class="border-gray-100">

            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-link text-blue-500"></i> Associations
                </h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Contact</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $followup->contact ? $followup->contact->first_name . ' ' . $followup->contact->last_name : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Customer</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $followup->customer?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Opportunity</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $followup->opportunity?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Lead</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $followup->lead?->title ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            @if($followup->description)
            <hr class="border-gray-100">

            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-align-left text-blue-500"></i> Description
                </h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $followup->description }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if ($confirmDelete)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-init="$nextTick(() => $refs.confirmBtn.focus())" @keydown.escape.window="$wire.set('confirmDelete', false)">
        <div class="fixed inset-0 bg-black/50 transition-opacity" @click="$wire.set('confirmDelete', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Delete Follow-up</h3>
                </div>
                <p class="text-sm text-gray-500">Are you sure you want to delete this follow-up? This action cannot be undone.</p>
                <div class="flex justify-end gap-3">
                    <button @click="$wire.set('confirmDelete', false)" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <button wire:click="delete" x-ref="confirmBtn" class="px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
