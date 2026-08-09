<?php

namespace App\Http\Livewire\Files;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\File;
use App\Models\Customer;

#[Layout('layouts.app', ['title' => 'Files'])]
class Index extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $customerFilter = '';
    public string $typeFilter = '';
    public string $viewMode = 'grid';
    public ?int $deleteId = null;
    public array $selected = [];
    public bool $selectAll = false;
    public bool $bulkDelete = false;
    public $uploadFile;
    public ?int $uploadCustomerId = null;
    public bool $showUpload = false;

    protected string $paginationTheme = 'tailwind';

    protected $listeners = ['fileUploaded' => '$refresh'];

    protected $rules = [
        'uploadFile' => 'required|file|max:10240',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCustomerFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function toggleView(): void
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    public function toggleUpload(): void
    {
        $this->showUpload = !$this->showUpload;
    }

    public function uploadFile(): void
    {
        $this->validate();

        $file = $this->uploadFile->store('uploads', 'public');
        $originalName = $this->uploadFile->getClientOriginalName();

        File::create([
            'name' => basename($file),
            'original_name' => $originalName,
            'path' => $file,
            'mime_type' => $this->uploadFile->getMimeType(),
            'size' => $this->uploadFile->getSize(),
            'customer_id' => $this->uploadCustomerId,
            'uploaded_by' => auth()->id(),
        ]);

        $this->reset(['uploadFile', 'uploadCustomerId']);
        $this->showUpload = false;
        $this->dispatch('fileUploaded');
        session()->flash('success', 'File uploaded successfully.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selected = $this->filesQuery()->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected(): void
    {
        $this->selectAll = false;
    }

    public function confirmBulkDelete(): void
    {
        $this->bulkDelete = true;
    }

    public function deleteSelected(): void
    {
        $count = count($this->selected);
        if ($count > 0) {
            File::whereIn('id', $this->selected)->get()->each(function ($file) {
                if ($file->path && \Storage::disk('public')->exists($file->path)) {
                    \Storage::disk('public')->delete($file->path);
                }
                $file->delete();
            });
            $this->selected = [];
            $this->selectAll = false;
            $this->bulkDelete = false;
            $this->dispatch('fileUploaded');
            session()->flash('success', $count . ' file(s) deleted successfully.');
        }
    }

    public function deleteFile(): void
    {
        if ($this->deleteId) {
            $file = File::findOrFail($this->deleteId);
            if ($file->path && \Storage::disk('public')->exists($file->path)) {
                \Storage::disk('public')->delete($file->path);
            }
            $file->delete();
            $this->deleteId = null;
            $this->dispatch('fileUploaded');
            session()->flash('success', 'File deleted successfully.');
        }
    }

    public function getFileIcon(string $mimeType): string
    {
        if (str_contains($mimeType, 'pdf')) return 'fa-file-pdf text-red-500 bg-red-50';
        if (str_contains($mimeType, 'image')) return 'fa-file-image text-green-500 bg-green-50';
        if (str_contains($mimeType, 'word') || str_contains($mimeType, 'document')) return 'fa-file-word text-blue-500 bg-blue-50';
        if (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheet')) return 'fa-file-excel text-emerald-500 bg-emerald-50';
        if (str_contains($mimeType, 'zip') || str_contains($mimeType, 'archive')) return 'fa-file-archive text-yellow-500 bg-yellow-50';
        if (str_contains($mimeType, 'video')) return 'fa-file-video text-purple-500 bg-purple-50';
        if (str_contains($mimeType, 'audio')) return 'fa-file-audio text-pink-500 bg-pink-50';
        if (str_contains($mimeType, 'text')) return 'fa-file-alt text-gray-500 bg-gray-50';
        return 'fa-file text-gray-400 bg-gray-50';
    }

    public function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    public function getCustomersProperty()
    {
        return Customer::orderBy('name')->get();
    }

    private function filesQuery()
    {
        return File::query()->with(['customer', 'uploader'])
            ->when($this->search, fn($q) => $q->where(function ($sub) {
                $sub->where('original_name', 'like', '%' . $this->search . '%')
                    ->orWhere('mime_type', 'like', '%' . $this->search . '%');
            }))
            ->when($this->customerFilter, fn($q) => $q->where('customer_id', $this->customerFilter))
            ->when($this->typeFilter, function ($q) {
                match($this->typeFilter) {
                    'pdf' => $q->where('mime_type', 'like', '%pdf%'),
                    'image' => $q->where('mime_type', 'like', '%image%'),
                    'document' => $q->where('mime_type', 'like', '%word%')->orWhere('mime_type', 'like', '%document%'),
                    'spreadsheet' => $q->where('mime_type', 'like', '%excel%')->orWhere('mime_type', 'like', '%spreadsheet%'),
                    default => null,
                };
            })
            ->latest();
    }

    public function render()
    {
        return view('livewire.files.index', [
            'files' => $this->filesQuery()->paginate(12),
        ]);
    }
}
