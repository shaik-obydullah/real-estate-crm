
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tags</h1>
            <p class="text-sm text-gray-500">Manage tags for categorization</p>
        </div>
        <div class="flex gap-2">
            <input wire:model.live="search" type="text" placeholder="Search tags..." class="px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <button wire:click="toggleCreateForm" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                <i class="fas {{ $showCreateForm ? 'fa-times' : 'fa-plus' }}"></i> {{ $showCreateForm ? 'Cancel' : 'Add Tag' }}
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($showCreateForm)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-data>
            <h3 class="text-sm font-semibold text-gray-900 mb-4">{{ $editId ? 'Edit Tag' : 'Create New Tag' }}</h3>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tag Name *</label>
                    <input wire:model="formName" type="text" placeholder="Enter tag name" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    @error('formName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                    <div class="flex gap-2 flex-wrap">
                        @foreach($colorPresets as $color)
                            <button type="button" wire:click="set 'formColor', '{{ $color }}'" class="w-8 h-8 rounded-full border-2 transition {{ $formColor === $color ? 'border-gray-900 scale-110' : 'border-transparent hover:scale-105' }}" style="background: {{ $color }};"></button>
                        @endforeach
                        <input wire:model="formColor" type="color" class="w-8 h-8 rounded-lg cursor-pointer border-0">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="toggleCreateForm" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                        {{ $editId ? 'Update' : 'Create' }} Tag
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($tags as $tag)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full flex-shrink-0" style="background: {{ $tag->color ?? '#6366f1' }}"></span>
                        <div>
                            <div class="font-medium text-gray-900">{{ $tag->name }}</div>
                            <div class="text-xs text-gray-500">{{ $tag->usage_count }} {{ Str::plural('usage', $tag->usage_count) }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <button wire:click="openEdit({{ $tag->id }})" class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition" title="Edit">
                            <i class="fas fa-pen text-xs"></i>
                        </button>
                        <button wire:click="deleteTag({{ $tag->id }})" wire:confirm="Delete this tag?" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" title="Delete">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <i class="fas fa-tags text-3xl text-gray-300 mb-3"></i>
                <p class="text-sm text-gray-500">No tags created yet</p>
            </div>
        @endforelse
    </div>
</div>
