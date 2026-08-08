<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-start gap-3">
        <a href="{{ route('calendar.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getTypeBadge($event->type) }}">
                    <i class="{{ $this->getTypeIcon($event->type) }} mr-1"></i>
                    {{ ucfirst($event->type) }}
                </span>
            </p>
        </div>
        <a href="{{ route('calendar.edit', $event) }}" wire:navigate class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-sm">
            <i class="fas fa-pen mr-1"></i> Edit
        </a>
    </div>

    {{-- Details --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 space-y-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-calendar-alt text-blue-500"></i> Event Details
            </h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Start Time</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ \App\Support\AppSettings::formatDateTime($event->start_time) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">End Time</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ \App\Support\AppSettings::formatDateTime($event->end_time) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Location</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $event->location ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">User</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $event->user?->name ?? '—' }}</dd>
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
                        <dd class="mt-1 text-sm text-gray-900">{{ $event->contact ? $event->contact->first_name . ' ' . $event->contact->last_name : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Customer</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $event->customer?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Opportunity</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $event->opportunity?->name ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            @if($event->description)
            <hr class="border-gray-100">

            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-align-left text-blue-500"></i> Description
                </h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $event->description }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
