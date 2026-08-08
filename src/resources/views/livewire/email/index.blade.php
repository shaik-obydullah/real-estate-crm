

<div class="flex h-[calc(100vh-8rem)] bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ composing: @entangle('composeMode') }">

    {{-- Left Panel: Folders + Email List --}}
    <div class="w-96 border-r border-gray-200 flex flex-col flex-shrink-0">
        {{-- Compose Button --}}
        <div class="p-4 border-b border-gray-100">
            <button wire:click="toggleCompose" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
                <i class="fas fa-plus"></i> Compose
            </button>
        </div>

        {{-- Folders --}}
        <div class="px-2 py-2 border-b border-gray-100">
            <div class="space-y-0.5">
                @foreach($folders as $folder)
                <button
                    wire:click="selectFolder('{{ $folder['name'] }}')"
                    class="w-full flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition {{ $selectedFolder === $folder['name'] ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}"
                >
                    <i class="fas {{ $folder['icon'] }} w-5 text-center {{ $selectedFolder === $folder['name'] ? 'text-blue-600' : 'text-gray-400' }}"></i>
                    <span class="flex-1 text-left">{{ $folder['label'] }}</span>
                    @if($folder['count'] > 0)
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-100 text-blue-700 rounded-full">{{ $folder['count'] }}</span>
                    @endif
                </button>
                @endforeach
            </div>
        </div>

        {{-- Search --}}
        <div class="px-4 py-3 border-b border-gray-100">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search emails..." class="w-full pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none bg-gray-50">
            </div>
        </div>

        {{-- Email List --}}
        <div class="flex-1 overflow-y-auto">
            @forelse($this->filteredEmails as $email)
            <button
                wire:click="selectEmail({{ $email['id'] }})"
                class="w-full flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition text-left border-b border-gray-50 {{ $selectedEmailId === $email['id'] ? 'bg-blue-50' : '' }} {{ !$email['is_read'] ? 'bg-blue-50/30' : '' }}"
            >
                <div class="relative flex-shrink-0">
                    <div class="w-10 h-10 bg-{{ $email['avatar_color'] }}-100 rounded-full flex items-center justify-center">
                        <span class="text-sm font-semibold text-{{ $email['avatar_color'] }}-700">{{ substr($email['from'], 0, 1) }}</span>
                    </div>
                    @if(!$email['is_read'])
                    <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-blue-500 border-2 border-white rounded-full"></span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-900 truncate {{ !$email['is_read'] ? 'font-bold' : '' }}">{{ $email['from'] }}</p>
                        <span class="text-[10px] text-gray-400 flex-shrink-0 ml-2">{{ \Carbon\Carbon::parse($email['date'])->format('M d') }}</span>
                    </div>
                    <p class="text-sm text-gray-700 truncate {{ !$email['is_read'] ? 'font-semibold' : '' }}">{{ $email['subject'] }}</p>
                    <p class="text-xs text-gray-400 truncate mt-0.5">{{ $email['preview'] }}</p>
                </div>
            </button>
            @empty
            <div class="p-8 text-center">
                <i class="fas fa-inbox text-2xl text-gray-300 mb-2"></i>
                <p class="text-sm text-gray-500">No emails in this folder</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Right Panel: Email Preview or Compose --}}
    <div class="flex-1 flex flex-col">
        @if($composeMode)
            {{-- Compose View --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">New Message</h3>
                <button wire:click="toggleCompose" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="flex-1 flex flex-col p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                        <input wire:model="to" type="email" placeholder="recipient@example.com" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <input wire:model="subject" type="text" placeholder="Email subject..." class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                        <textarea wire:model="body" rows="14" placeholder="Write your message..." class="w-full px-4 py-3 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none"></textarea>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-4 pt-4 border-t border-gray-100">
                    <button wire:click="sendEmail" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
                        <i class="fas fa-paper-plane"></i> Send
                    </button>
                    <button class="p-2.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition" title="Attach File">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <button wire:click="toggleCompose" class="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Discard">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

        @elseif($selectedEmail)
            {{-- Email Preview --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <button wire:click="$set('selectedEmailId', null)" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition lg:hidden">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $selectedEmail['subject'] }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">From: {{ $selectedEmail['from'] }} &lt;{{ $selectedEmail['email'] }}&gt;</p>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition" title="Reply">
                        <i class="fas fa-reply text-sm"></i>
                    </button>
                    <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition" title="Forward">
                        <i class="fas fa-share text-sm"></i>
                    </button>
                    <button wire:click="deleteEmail({{ $selectedEmail['id'] }})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-{{ $selectedEmail['avatar_color'] }}-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-lg font-semibold text-{{ $selectedEmail['avatar_color'] }}-700">{{ substr($selectedEmail['from'], 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $selectedEmail['from'] }}</p>
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($selectedEmail['date'])->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
                <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $selectedEmail['body'] }}</div>
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <p class="text-xs text-gray-400 mb-3">Quick Reply</p>
                    <form class="flex gap-3">
                        <input type="text" placeholder="Type your reply..." class="flex-1 px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>

        @else
            {{-- Empty State --}}
            <div class="flex-1 flex items-center justify-center">
                <div class="text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-envelope text-3xl text-gray-300"></i>
                    </div>
                    <p class="text-lg font-medium text-gray-900">Select an email</p>
                    <p class="text-sm text-gray-500 mt-1">Choose an email from the list to read</p>
                </div>
            </div>
        @endif
    </div>
</div>

@if(session('success'))
<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition class="fixed bottom-4 right-4 z-50 px-4 py-3 bg-green-600 text-white text-sm font-medium rounded-lg shadow-lg flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif
