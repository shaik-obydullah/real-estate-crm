

<div class="space-y-6" x-data="{ showEditor: @entangle('showEditor') }">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notes</h1>
            <p class="text-sm text-gray-500">Manage your notes and memos</p>
        </div>
        <button wire:click="openEditor" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
            <i class="fas fa-plus"></i> New Note
        </button>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Inline Editor --}}
    <div x-show="showEditor" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-cloak>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">{{ $editingNoteId ? 'Edit Note' : 'New Note' }}</h3>
            <button wire:click="cancelEdit" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input wire:model="title" type="text" placeholder="Note title..." class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('title') border-red-300 @enderror">
                @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                <textarea wire:model="content" rows="6" placeholder="Write your note..." class="w-full px-4 py-3 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none @error('content') border-red-300 @enderror"></textarea>
                @error('content') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                    <select wire:model="customerId" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="">No customer</option>
                        @foreach($this->customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 cursor-pointer px-4 py-2.5 border border-gray-200 rounded-lg hover:bg-gray-50 transition w-full">
                        <input type="checkbox" wire:model="isPinned" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <i class="fas fa-thumbtack text-gray-400"></i>
                        <span class="text-sm text-gray-700">Pin this note</span>
                    </label>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button wire:click="saveNote" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
                    <i class="fas fa-save"></i> {{ $editingNoteId ? 'Update' : 'Save' }}
                </button>
                <button wire:click="cancelEdit" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            </div>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search notes..." class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none bg-white">
        </div>
        <select wire:model.live="customerFilter" class="px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
            <option value="">All Customers</option>
            @foreach($this->customers as $customer)
            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Notes Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($notes as $note)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition group relative">
            @if($note->is_pinned)
            <div class="absolute top-3 right-3">
                <i class="fas fa-thumbtack text-yellow-500 text-xs" title="Pinned"></i>
            </div>
            @endif
            <div class="mb-3">
                <h3 class="text-sm font-bold text-gray-900 pr-6">{{ $note->title }}</h3>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed mb-4 line-clamp-3">{{ Str::limit($note->content, 150) }}</p>
            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <div class="flex items-center gap-2">
                    @if($note->customer)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-700">
                        <i class="fas fa-building mr-1"></i>{{ Str::limit($note->customer->name, 20) }}
                    </span>
                    @endif
                    <span class="text-[10px] text-gray-400">{{ $note->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                    <button wire:click="togglePin({{ $note->id }})" class="p-1.5 {{ $note->is_pinned ? 'text-yellow-500 hover:text-yellow-600' : 'text-gray-400 hover:text-yellow-500' }} hover:bg-yellow-50 rounded transition" title="{{ $note->is_pinned ? 'Unpin' : 'Pin' }}">
                        <i class="fas fa-thumbtack text-xs"></i>
                    </button>
                    <button wire:click="editNote({{ $note->id }})" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition" title="Edit">
                        <i class="fas fa-edit text-xs"></i>
                    </button>
                    <button wire:click="confirmDelete({{ $note->id }})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition" title="Delete">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-sticky-note text-2xl text-gray-400"></i>
            </div>
            <p class="text-sm font-medium text-gray-900 mb-1">No notes found</p>
            <p class="text-sm text-gray-500 mb-4">Create your first note to get started</p>
            <button wire:click="openEditor" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus"></i> New Note
            </button>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Showing {{ $notes->firstItem() ?? 0 }} to {{ $notes->lastItem() ?? 0 }} of {{ $notes->total() }} notes
        </p>
        {{ $notes->links() }}
    </div>
</div>

{{-- Delete Confirmation Modal --}}
@if($deleteId)
<div class="fixed inset-0 z-50 flex items-center justify-center" x-data x-init="$nextTick(() => $refs.confirmBtn.focus())" @keydown.escape.window="$wire.$set('deleteId', null)">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="$wire.$set('deleteId', null)"></div>
    <div class="relative bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4 z-10">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Delete Note</h3>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete this note?</p>
        <div class="flex items-center justify-end gap-3">
            <button wire:click="$set('deleteId', null)" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            <button x-ref="confirmBtn" wire:click="deleteNote" class="px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition shadow-sm">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
        </div>
    </div>
</div>
@endif
