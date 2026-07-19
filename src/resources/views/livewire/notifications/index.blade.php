
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <p class="text-sm text-gray-500">Stay updated on important events</p>
        </div>
        <div class="flex items-center gap-3">
            @if($unreadCount > 0)
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">{{ $unreadCount }} unread</span>
            @endif
            <button wire:click="markAllAsRead" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-check-double"></i> Mark all read
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-1">
        <div class="flex gap-1">
            @php
                $filters = [
                    'all' => ['label' => 'All', 'icon' => 'fa-list'],
                    'unread' => ['label' => 'Unread', 'icon' => 'fa-circle'],
                    'read' => ['label' => 'Read', 'icon' => 'fa-check-circle'],
                ];
            @endphp
            @foreach($filters as $key => $f)
                <button wire:click="$set('filter', '{{ $key }}')" :class="$wire.filter === '{{ $key }}' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'" class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition flex-1 justify-center">
                    <i class="fas {{ $f['icon'] }}"></i> {{ $f['label'] }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Notification List -->
    <div class="space-y-2">
        @forelse($notifications as $notification)
            @php
                $typeIcons = [
                    'lead' => ['icon' => 'fa-funnel-dollar', 'color' => 'blue'],
                    'invoice' => ['icon' => 'fa-file-invoice', 'color' => 'green'],
                    'task' => ['icon' => 'fa-check-square', 'color' => 'purple'],
                    'ticket' => ['icon' => 'fa-ticket-alt', 'color' => 'yellow'],
                    'payment' => ['icon' => 'fa-credit-card', 'color' => 'green'],
                    'message' => ['icon' => 'fa-comment', 'color' => 'blue'],
                    'mention' => ['icon' => 'fa-at', 'color' => 'red'],
                    'system' => ['icon' => 'fa-cog', 'color' => 'gray'],
                ];
                $nType = $typeIcons[$notification->type] ?? ['icon' => 'fa-bell', 'color' => 'blue'];
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition {{ is_null($notification->read_at) ? 'border-l-4 border-l-blue-500' : '' }}">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-{{ $nType['color'] }}-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $nType['icon'] }} text-{{ $nType['color'] }}-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h4 class="text-sm font-medium {{ is_null($notification->read_at) ? 'text-gray-900' : 'text-gray-700' }}">{{ $notification->title }}</h4>
                                <p class="text-sm text-gray-500 mt-0.5 line-clamp-2">{{ $notification->message }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                @if(is_null($notification->read_at))
                                    <button wire:click="markAsRead({{ $notification->id }})" class="text-gray-400 hover:text-blue-600" title="Mark as read">
                                        <i class="fas fa-envelope-open text-xs"></i>
                                    </button>
                                @endif
                                <button wire:click="deleteNotification({{ $notification->id }})" wire:confirm="Delete this notification?" class="text-gray-400 hover:text-red-600" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-2 flex items-center gap-3 text-xs text-gray-400">
                            <span><i class="fas fa-clock mr-1"></i>{{ $notification->created_at->diffForHumans() }}</span>
                            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">{{ ucfirst($notification->type ?? 'general') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <i class="fas fa-bell-slash text-3xl text-gray-300 mb-3"></i>
                <p class="text-sm text-gray-500">No notifications found</p>
            </div>
        @endforelse
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        {{ $notifications->links() }}
    </div>
</div>
