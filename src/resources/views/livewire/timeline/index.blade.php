

<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Timeline</h1>
            <p class="text-sm text-gray-500">Activity timeline across the CRM</p>
        </div>
        <div class="flex items-center gap-3">
            <select wire:model.live="customerId" class="px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                <option value="">All Customers</option>
                @foreach($this->customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
            <button wire:click="loadTimeline" class="p-2.5 text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition" title="Refresh">
                <i class="fas fa-sync-alt text-sm"></i>
            </button>
        </div>
    </div>

    {{-- Legend --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-wrap items-center gap-4 text-xs">
            <span class="font-medium text-gray-500">Legend:</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Invoice</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Proposal</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span> Meeting</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span> Call</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Email</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span> Task</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span> Note</span>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @if(count($events) > 0)
        <div class="relative">
            {{-- Vertical Line --}}
            <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>

            <div class="space-y-8">
                @foreach($events as $index => $event)
                @php
                    $colorClass = match($event->color) {
                        'green' => 'bg-green-500',
                        'blue' => 'bg-blue-500',
                        'purple' => 'bg-purple-500',
                        'indigo' => 'bg-indigo-500',
                        'yellow' => 'bg-yellow-500',
                        'red' => 'bg-red-500',
                        'pink' => 'bg-pink-500',
                        default => 'bg-gray-400',
                    };
                    $iconBg = match($event->color) {
                        'green' => 'bg-green-100 text-green-600',
                        'blue' => 'bg-blue-100 text-blue-600',
                        'purple' => 'bg-purple-100 text-purple-600',
                        'indigo' => 'bg-indigo-100 text-indigo-600',
                        'yellow' => 'bg-yellow-100 text-yellow-600',
                        'red' => 'bg-red-100 text-red-600',
                        'pink' => 'bg-pink-100 text-pink-600',
                        default => 'bg-gray-100 text-gray-500',
                    };
                    $typeLabel = match($event->event_type) {
                        'call' => 'Call',
                        'email' => 'Email',
                        'meeting' => 'Meeting',
                        'proposal' => 'Proposal',
                        'invoice' => 'Invoice',
                        'created' => 'Created',
                        'Note' => 'Note',
                        default => ucfirst($event->event_type ?? 'Activity'),
                    };
                    $typeBadgeColor = match($event->color) {
                        'green' => 'bg-green-100 text-green-700',
                        'blue' => 'bg-blue-100 text-blue-700',
                        'purple' => 'bg-purple-100 text-purple-700',
                        'indigo' => 'bg-indigo-100 text-indigo-700',
                        'yellow' => 'bg-yellow-100 text-yellow-700',
                        default => 'bg-gray-100 text-gray-600',
                    };
                @endphp
                <div class="relative flex items-start gap-4 pl-0">
                    {{-- Dot --}}
                    <div class="absolute left-4 w-4 h-4 rounded-full {{ $colorClass }} border-4 border-white shadow-sm z-10 mt-1"></div>

                    {{-- Content --}}
                    <div class="ml-14 flex-1">
                        <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 {{ $iconBg }}">
                                        <i class="fas {{ $event->icon }} text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="text-sm font-semibold text-gray-900">{{ $event->title }}</p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium {{ $typeBadgeColor }}">{{ $typeLabel }}</span>
                                            @if(!empty($event->status))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium {{ $event->status === 'completed' ? 'bg-green-100 text-green-700' : ($event->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">{{ ucfirst($event->status) }}</span>
                                            @endif
                                        </div>
                                        @if($event->description)
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ Str::limit($event->description, 120) }}</p>
                                        @endif
                                        <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                                            <span class="flex items-center gap-1">
                                                <i class="far fa-clock"></i>
                                                {{ \Carbon\Carbon::parse($event->date)->format('M d, Y \a\t g:i A') }}
                                            </span>
                                            @if($event->customer)
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-building"></i>
                                                {{ $event->customer }}
                                            </span>
                                            @endif
                                            @if($event->person)
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-user"></i>
                                                {{ $event->person }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="text-center py-16">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-stream text-2xl text-gray-400"></i>
            </div>
            <p class="text-sm font-medium text-gray-900 mb-1">No activity yet</p>
            <p class="text-sm text-gray-500">Timeline events will appear here as activities are recorded</p>
        </div>
        @endif
    </div>
</div>
