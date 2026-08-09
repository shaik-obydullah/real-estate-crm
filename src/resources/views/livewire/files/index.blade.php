

<div class="space-y-6" x-data="{ viewMode: @entangle('viewMode'), showUpload: @entangle('showUpload') }">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Files</h1>
            <p class="text-sm text-gray-500">Manage uploaded files and documents</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="toggleView" class="p-2.5 text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition" title="Toggle View">
                <i class="fas" :class="viewMode === 'grid' ? 'fa-list' : 'fa-th-large'"></i>
            </button>
            <button wire:click="toggleUpload" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
                <i class="fas fa-cloud-upload-alt"></i> Upload File
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Upload Zone --}}
    <div x-show="showUpload" x-transition class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-cloak>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Upload File</h3>
            <button wire:click="toggleUpload" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form wire:submit.prevent="uploadFile" class="space-y-4">
            <div
                x-data="{ dragging: false }"
                x-on:dragover.prevent="dragging = true"
                x-on:dragleave.prevent="dragging = false"
                x-on:drop.prevent="dragging = false; $refs.fileInput.click()"
                :class="dragging ? 'border-blue-400 bg-blue-50' : 'border-gray-300 bg-gray-50'"
                class="border-2 border-dashed rounded-xl p-8 text-center transition cursor-pointer"
                @click="$refs.fileInput.click()"
            >
                <div x-show="!$wire.uploadFile">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-300 mb-3"></i>
                    <p class="text-sm font-medium text-gray-700">Drag & drop your file here</p>
                    <p class="text-xs text-gray-400 mt-1">or click to browse (max 10MB)</p>
                </div>
                <div x-show="$wire.uploadFile" class="flex items-center justify-center gap-3">
                    <i class="fas fa-file text-blue-500 text-2xl"></i>
                    <span class="text-sm text-gray-700 font-medium" x-text="$wire.uploadFile?.name || 'File selected'"></span>
                </div>
                <input type="file" wire:model="uploadFile" x-ref="fileInput" class="hidden" @change="if($refs.fileInput.files.length) $wire.set('uploadFile', $refs.fileInput.files[0])">
            </div>
            @error('uploadFile') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Link to Customer (optional)</label>
                <select wire:model="uploadCustomerId" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">No customer</option>
                    @foreach($this->customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
                    <i class="fas fa-upload"></i> Upload
                </button>
                <button type="button" wire:click="toggleUpload" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            </div>
        </form>
    </div>

    {{-- Search & Filters --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search files..." class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none bg-white">
        </div>
        <select wire:model.live="customerFilter" class="px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
            <option value="">All Customers</option>
            @foreach($this->customers as $customer)
            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="typeFilter" class="px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
            <option value="">All Types</option>
            <option value="pdf">PDF</option>
            <option value="image">Images</option>
            <option value="document">Documents</option>
            <option value="spreadsheet">Spreadsheets</option>
        </select>
        @if (count($selected) > 0)
        <button wire:click="confirmBulkDelete" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition shadow-sm">
            <i class="fas fa-trash"></i> Delete Selected ({{ count($selected) }})
        </button>
        @endif
    </div>

    {{-- Files Grid View --}}
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($files as $file)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition group relative">
            <div class="absolute top-3 right-3">
                <input type="checkbox" wire:model.live="selected" value="{{ $file->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            </div>
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 {{ $this->getFileIcon($file->mime_type) }}">
                    <i class="fas {{ explode(' ', $this->getFileIcon($file->mime_type))[0] }} text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $file->original_name }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $this->formatFileSize($file->size) }}</p>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                <div class="space-y-1">
                    @if($file->customer)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-700">
                        <i class="fas fa-building mr-1"></i>{{ Str::limit($file->customer->name, 15) }}
                    </span>
                    @endif
                    <p class="text-[10px] text-gray-400">{{ $file->created_at->format('M d, Y') }}</p>
                </div>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                    <a href="{{ asset('storage/' . $file->path) }}" target="_blank" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition" title="Download">
                        <i class="fas fa-download text-xs"></i>
                    </a>
                    <button wire:click="confirmDelete({{ $file->id }})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition" title="Delete">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-folder-open text-2xl text-gray-400"></i>
            </div>
            <p class="text-sm font-medium text-gray-900 mb-1">No files found</p>
            <p class="text-sm text-gray-500 mb-4">Upload your first file to get started</p>
            <button wire:click="toggleUpload" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                <i class="fas fa-cloud-upload-alt"></i> Upload File
            </button>
        </div>
        @endforelse
    </div>

    {{-- Files List View --}}
    <div x-show="viewMode === 'list'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" x-cloak>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-6 py-3 w-10">
                            <input type="checkbox" wire:model="selectAll" wire:change="toggleSelectAll()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 font-medium text-gray-500">File</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Type</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Size</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">Customer</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">Uploaded By</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden xl:table-cell">Date</th>
                        <th class="px-6 py-3 font-medium text-gray-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($files as $file)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <input type="checkbox" wire:model.live="selected" value="{{ $file->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $this->getFileIcon($file->mime_type) }}">
                                    <i class="fas {{ explode(' ', $this->getFileIcon($file->mime_type))[0] }} text-sm"></i>
                                </div>
                                <p class="font-medium text-gray-900 truncate max-w-xs">{{ $file->original_name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden md:table-cell text-xs">{{ $file->mime_type }}</td>
                        <td class="px-6 py-4 text-gray-500 hidden md:table-cell">{{ $this->formatFileSize($file->size) }}</td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            @if($file->customer)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">{{ Str::limit($file->customer->name, 20) }}</span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden lg:table-cell text-xs">{{ $file->uploader->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-500 hidden xl:table-cell text-xs">{{ $file->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ asset('storage/' . $file->path) }}" target="_blank" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Download">
                                    <i class="fas fa-download text-sm"></i>
                                </a>
                                <button wire:click="confirmDelete({{ $file->id }})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-gray-500">No files found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $files->links() }}
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
                <h3 class="text-lg font-semibold text-gray-900">Delete File</h3>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete this file? The file will be permanently removed.</p>
        <div class="flex items-center justify-end gap-3">
            <button wire:click="$set('deleteId', null)" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            <button x-ref="confirmBtn" wire:click="deleteFile" class="px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition shadow-sm">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
        </div>
    </div>
</div>
@endif

{{-- Bulk Delete Confirmation Modal --}}
@if($bulkDelete)
<div class="fixed inset-0 z-50 flex items-center justify-center" x-data x-init="$nextTick(() => $refs.bulkConfirmBtn.focus())" @keydown.escape.window="$wire.$set('bulkDelete', false)">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="$wire.$set('bulkDelete', false)"></div>
    <div class="relative bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4 z-10">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Delete {{ count($selected) }} Files</h3>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete the {{ count($selected) }} selected files? The files will be permanently removed.</p>
        <div class="flex items-center justify-end gap-3">
            <button wire:click="$set('bulkDelete', false)" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            <button x-ref="bulkConfirmBtn" wire:click="deleteSelected" class="px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition shadow-sm">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
        </div>
    </div>
</div>
@endif
