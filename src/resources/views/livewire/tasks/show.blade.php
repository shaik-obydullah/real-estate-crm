<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-start gap-3">
        <a href="{{ route('tasks.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $task->title }}</h1>
            <p class="text-sm text-gray-500 mt-1 flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getPriorityColor($task->priority) }}">
                    <i class="{{ $this->getPriorityIcon($task->priority) }} text-[10px]"></i>
                    {{ ucfirst($task->priority) }} Priority
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($task->status) }}">
                    <i class="fas fa-circle text-[6px] mr-1"></i>
                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                </span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="confirmDelete" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition shadow-sm">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
            <a href="{{ route('tasks.edit', $task) }}" wire:navigate class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-sm">
                <i class="fas fa-pen mr-1"></i> Edit
            </a>
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
                <i class="fas fa-tasks text-blue-500"></i> Task Details
            </h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $task->due_date ? \App\Support\AppSettings::formatDate($task->due_date) : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Due Time</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $task->due_time ? \App\Support\AppSettings::formatTime($task->due_time) : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $task->assignedTo?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $task->created_at->format('M d, Y') }}</dd>
                </div>
            </dl>

            <hr class="border-gray-100">

            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-link text-blue-500"></i> Related Records
                </h3>
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Customer</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $task->customer?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Opportunity</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $task->opportunity?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Lead</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $task->lead?->title ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            @if($task->description)
            <hr class="border-gray-100">

            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-align-left text-blue-500"></i> Description
                </h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $task->description }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
@if ($confirmingDelete)
<div class="fixed inset-0 z-50 flex items-center justify-center" x-data x-init="$nextTick(() => $refs.confirmBtn.focus())" @keydown.escape.window="$wire.set('confirmingDelete', false)">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="$wire.set('confirmingDelete', false)"></div>
    <div class="relative bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4 z-10 transform transition-all">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Delete Task</h3>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete this task?</p>
        <div class="flex items-center justify-end gap-3">
            <button wire:click="$set('confirmingDelete', false)" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            <button x-ref="confirmBtn" wire:click="delete" class="px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition shadow-sm">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
        </div>
    </div>
</div>
@endif
