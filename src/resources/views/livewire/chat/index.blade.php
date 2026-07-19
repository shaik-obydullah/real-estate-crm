

<div class="flex h-[calc(100vh-8rem)] bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" wire:poll.5s="loadContacts">
    {{-- Contacts Sidebar --}}
    <div class="w-80 border-r border-gray-200 flex flex-col flex-shrink-0">
        <div class="p-4 border-b border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-bold text-gray-900">Messages</h2>
                <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-edit text-sm"></i>
                </button>
            </div>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search contacts..." class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none bg-gray-50">
            </div>
        </div>
        <div class="flex-1 overflow-y-auto">
            @forelse($chatContacts as $contact)
            <button
                wire:click="selectContact({{ $contact->id }})"
                class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition text-left {{ $selectedContactId === $contact->id ? 'bg-blue-50 border-r-2 border-blue-600' : '' }}"
            >
                <div class="relative flex-shrink-0">
                    <div class="w-11 h-11 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-sm font-semibold text-blue-700">{{ substr($contact->name, 0, 1) }}</span>
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $contact->name }}</p>
                        @php $unread = $this->getUnreadCount($contact->id); @endphp
                        @if($unread > 0)
                        <span class="flex-shrink-0 w-5 h-5 bg-blue-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $unread > 9 ? '9+' : $unread }}</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ $this->getLastMessagePreview($contact->id) }}</p>
                </div>
            </button>
            @empty
            <div class="p-8 text-center">
                <i class="fas fa-users text-2xl text-gray-300 mb-2"></i>
                <p class="text-sm text-gray-500">No contacts found</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Chat Area --}}
    <div class="flex-1 flex flex-col">
        @if($selectedContactId)
            @php $activeContact = $contacts->firstWhere('id', $selectedContactId); @endphp
            {{-- Chat Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-sm font-semibold text-blue-700">{{ substr($activeContact->name ?? '?', 0, 1) }}</span>
                        </div>
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-400 border-2 border-white rounded-full"></span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $activeContact->name ?? '' }}</p>
                        <p class="text-xs text-green-500">Online</p>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition" title="Voice Call">
                        <i class="fas fa-phone text-sm"></i>
                    </button>
                    <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition" title="Video Call">
                        <i class="fas fa-video text-sm"></i>
                    </button>
                    <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition" title="More Options">
                        <i class="fas fa-ellipsis-v text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4" id="chatMessages">
                @forelse($messages as $msg)
                    @php $isSent = $msg->sender_id === auth()->id(); @endphp
                    <div class="flex items-end gap-2 {{ $isSent ? 'justify-end' : 'justify-start' }}">
                        @if(!$isSent)
                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-semibold text-gray-600">{{ substr($msg->sender->name ?? '?', 0, 1) }}</span>
                        </div>
                        @endif
                        <div class="max-w-xs lg:max-w-md">
                            <div class="px-4 py-2.5 rounded-2xl {{ $isSent ? 'bg-blue-600 text-white rounded-br-md' : 'bg-gray-100 text-gray-900 rounded-bl-md' }}">
                                <p class="text-sm leading-relaxed">{{ $msg->message }}</p>
                            </div>
                            <div class="flex items-center gap-1 mt-1 {{ $isSent ? 'justify-end' : 'justify-start' }}">
                                <span class="text-[10px] text-gray-400">{{ $msg->created_at->format('g:i A') }}</span>
                                @if($isSent)
                                <span class="text-[10px] {{ $msg->is_read ? 'text-blue-500' : 'text-gray-400' }}">
                                    <i class="fas {{ $msg->is_read ? 'fa-check-double' : 'fa-check' }}"></i>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                <div class="flex flex-col items-center justify-center h-full text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-comments text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Start a conversation</p>
                    <p class="text-xs text-gray-500 mt-1">Send a message to {{ $activeContact->name ?? '' }}</p>
                </div>
                @endforelse
            </div>

            {{-- Message Input --}}
            <div class="px-6 py-4 border-t border-gray-100">
                <form wire:submit.prevent="sendMessage" class="flex items-center gap-3">
                    <button type="button" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition" title="Attach File">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <div class="flex-1 relative">
                        <input wire:model="message" type="text" placeholder="Type a message..." class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none pr-12">
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" title="Emoji">
                            <i class="fas fa-smile"></i>
                        </button>
                    </div>
                    <button type="submit" class="p-2.5 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition shadow-sm" title="Send">
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </form>
            </div>
        @else
            <div class="flex-1 flex items-center justify-center">
                <div class="text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-comments text-3xl text-gray-300"></i>
                    </div>
                    <p class="text-lg font-medium text-gray-900">Select a conversation</p>
                    <p class="text-sm text-gray-500 mt-1">Choose a contact to start messaging</p>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:load', () => {
    Livewire.on('messageSent', () => {
        const el = document.getElementById('chatMessages');
        if (el) el.scrollTop = el.scrollHeight;
    });
});
</script>
@endpush
