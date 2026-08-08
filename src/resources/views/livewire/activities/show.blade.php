<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-start gap-3">
        <a href="{{ route('activities.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $activity->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getTypeBadge($activity->type) }}">
                    <i class="{{ $activity->type === 'call' ? 'fas fa-phone' : ($activity->type === 'email' ? 'fas fa-envelope' : ($activity->type === 'meeting' ? 'fas fa-users' : ($activity->type === 'note' ? 'fas fa-sticky-note' : ($activity->type === 'task' ? 'fas fa-check-circle' : 'fas fa-circle')))) }} mr-1"></i>
                    {{ ucfirst($activity->type) }}
                </span>
            </p>
        </div>
        <a href="{{ route('activities.edit', $activity) }}" wire:navigate class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-sm">
            <i class="fas fa-pen mr-1"></i> Edit
        </a>
    </div>

    {{-- Details --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 space-y-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-calendar-check text-blue-500"></i> Activity Details
            </h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ \App\Support\AppSettings::formatDate($activity->date) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Time</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $activity->time ? \App\Support\AppSettings::formatTime($activity->time) : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Duration</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $activity->duration ? $activity->duration . ' minutes' : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Outcome</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $activity->outcome ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $activity->assignedTo?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created By</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $activity->creator?->name ?? '—' }}</dd>
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
                        <dd class="mt-1 text-sm text-gray-900">{{ $activity->contact ? $activity->contact->first_name . ' ' . $activity->contact->last_name : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Customer</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $activity->customer?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Opportunity</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $activity->opportunity?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Lead</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $activity->lead?->title ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            @if($activity->description)
            <hr class="border-gray-100">

            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-align-left text-blue-500"></i> Description
                </h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $activity->description }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
