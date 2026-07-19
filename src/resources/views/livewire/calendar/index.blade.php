

<div class="space-y-6" x-data="{ showEventForm: false }">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Calendar</h1>
            <p class="text-sm text-gray-500">View and manage your scheduled events</p>
        </div>
        <a href="{{ route('calendar.create') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
            <i class="fas fa-plus"></i> New Event
        </a>
    </div>

    @if (session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Calendar Grid --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                {{-- Month Navigation --}}
                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <button wire:click="previousMonth" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <h2 class="text-lg font-semibold text-gray-900 min-w-[180px] text-center">{{ $this->month_name }}</h2>
                            <button wire:click="nextMonth" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="flex items-center gap-3">
                            <select wire:model.live="typeFilter" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                                <option value="">All Types</option>
                                <option value="meeting">Meeting</option>
                                <option value="call">Call</option>
                                <option value="task">Task</option>
                            </select>
                            <button wire:click="goToToday" class="px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition">Today</button>
                        </div>
                    </div>
                </div>

                {{-- Calendar Grid --}}
                <div class="p-4">
                    {{-- Day Headers --}}
                    <div class="grid grid-cols-7 gap-1 mb-2">
                        @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayName)
                        <div class="text-center text-xs font-medium text-gray-500 py-2">{{ $dayName }}</div>
                        @endforeach
                    </div>

                    {{-- Days Grid --}}
                    <div class="grid grid-cols-7 gap-1">
                        @foreach($this->days as $day)
                        <button
                            wire:click="selectDate('{{ $day['date'] }}')"
                            class="relative min-h-[80px] p-2 rounded-lg text-left transition border
                                {{ $day['isToday'] ? 'ring-2 ring-blue-500 border-blue-200' : 'border-transparent hover:bg-gray-50' }}
                                {{ $selectedDate === $day['date'] ? 'bg-blue-50 border-blue-300' : '' }}
                                {{ !$day['isCurrentMonth'] ? 'opacity-40' : '' }}
                                {{ $day['isWeekend'] ? 'bg-gray-50/50' : '' }}"
                        >
                            <span class="text-sm font-medium {{ $day['isToday'] ? 'text-blue-600' : ($day['isCurrentMonth'] ? 'text-gray-900' : 'text-gray-400') }}">{{ $day['day'] }}</span>

                            {{-- Event Dots --}}
                            @if(isset($events[$day['date']]))
                            <div class="mt-1 flex flex-wrap gap-0.5">
                                @foreach($events[$day['date']]->take(3) as $event)
                                <span class="block w-2 h-2 rounded-full {{ $this->getTypeColor($event->type) }}" title="{{ $event->title }}"></span>
                                @endforeach
                                @if($events[$day['date']]->count() > 3)
                                <span class="text-[9px] text-gray-400">+{{ $events[$day['date']]->count() - 3 }}</span>
                                @endif
                            </div>
                            @endif
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Legend --}}
                <div class="px-4 pb-4 flex flex-wrap gap-4">
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span> Meeting
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Call
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span> Task
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span> Other
                    </div>
                </div>
            </div>
        </div>

        {{-- Selected Day Events --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 sticky top-6">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">
                        @if($selectedDate)
                            Events for {{ \Carbon\Carbon::parse($selectedDate)->format('l, M d') }}
                        @else
                            Select a day to view events
                        @endif
                    </h3>
                </div>

                <div class="p-4">
                    @if($selectedDate)
                        @if($this->selected_day_events->count())
                        <div class="space-y-3">
                            @foreach($this->selected_day_events as $event)
                            <div class="p-3 rounded-lg border border-gray-100 hover:border-gray-200 transition group">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 w-8 h-8 rounded-lg {{ $this->getTypeTextColor($event->type) }} flex items-center justify-center flex-shrink-0">
                                        <i class="{{ $this->getTypeIcon($event->type) }} text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium text-sm text-gray-900 truncate">{{ $event->title }}</div>
                                        <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                            <i class="fas fa-clock"></i>
                                            {{ $event->start_time->format('g:i A') }}
                                            @if($event->end_time)
                                            - {{ $event->end_time->format('g:i A') }}
                                            @endif
                                        </div>
                                        @if($event->location)
                                        <div class="flex items-center gap-1 mt-1 text-xs text-gray-400">
                                            <i class="fas fa-map-marker-alt"></i> {{ $event->location }}
                                        </div>
                                        @endif
                                        @if($event->customer)
                                        <div class="flex items-center gap-1 mt-1 text-xs text-gray-400">
                                            <i class="fas fa-building"></i> {{ $event->customer->name }}
                                        </div>
                                        @endif
                                    </div>
                                    <div class="opacity-0 group-hover:opacity-100 transition flex items-center gap-1">
                                        <a href="{{ route('calendar.show', $event) }}" wire:navigate class="p-1 text-gray-400 hover:text-blue-600 rounded" title="View">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-calendar-day text-gray-400 text-xl"></i>
                            </div>
                            <p class="text-sm text-gray-500">No events for this day</p>
                        </div>
                        @endif
                    @else
                    <div class="text-center py-8">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-hand-pointer text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-sm text-gray-500">Click on a day in the calendar to view events</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Upcoming Events Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">All Events This Month</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-6 py-3 font-medium text-gray-500">Type</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Event</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Start</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">End</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">Location</th>
                        <th class="px-6 py-3 font-medium text-gray-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($this->events->flatten() as $event)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getTypeTextColor($event->type) }}">
                                <i class="{{ $this->getTypeIcon($event->type) }} text-[10px]"></i>
                                {{ ucfirst($event->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $event->title }}</td>
                        <td class="px-6 py-4 text-gray-500 hidden md:table-cell">{{ $event->start_time?->format('M d, Y g:i A') ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-500 hidden md:table-cell">{{ $event->end_time?->format('M d, Y g:i A') ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-500 hidden lg:table-cell">{{ $event->location ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1" x-data>
                                <a href="{{ route('calendar.show', $event) }}" wire:navigate class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition" title="View">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('calendar.edit', $event) }}" wire:navigate class="p-2 text-gray-400 hover:text-amber-600 rounded-lg hover:bg-amber-50 transition" title="Edit">
                                    <i class="fas fa-pen text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-calendar text-gray-400 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">No events this month</p>
                                    <p class="text-xs text-gray-500">Create your first event to get started</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
