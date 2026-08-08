<?php

namespace App\Http\Livewire\Notes;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Note;
use App\Models\Customer;

#[Layout('layouts.app', ['title' => 'Notes'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $customerFilter = '';
    public bool $showEditor = false;
    public ?int $editingNoteId = null;
    public string $title = '';
    public string $content = '';
    public ?int $customerId = null;
    public bool $isPinned = false;
    public ?int $deleteId = null;

    protected string $paginationTheme = 'tailwind';

    protected $listeners = ['noteSaved' => '$refresh'];

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCustomerFilter(): void
    {
        $this->resetPage();
    }

    public function openEditor(): void
    {
        $this->showEditor = true;
        $this->editingNoteId = null;
        $this->title = '';
        $this->content = '';
        $this->customerId = null;
        $this->isPinned = false;
    }

    public function editNote(int $id): void
    {
        $note = Note::findOrFail($id);
        $this->editingNoteId = $note->id;
        $this->title = $note->title;
        $this->content = $note->content;
        $this->customerId = $note->customer_id;
        $this->isPinned = $note->is_pinned;
        $this->showEditor = true;
    }

    public function saveNote(): void
    {
        $this->validate();

        Note::updateOrCreate(
            ['id' => $this->editingNoteId],
            [
                'title' => $this->title,
                'content' => $this->content,
                'customer_id' => $this->customerId,
                'is_pinned' => $this->isPinned,
                'created_by' => auth()->id(),
            ]
        );

        $this->showEditor = false;
        $this->resetEditor();
        $this->dispatch('noteSaved');
        session()->flash('success', $this->editingNoteId ? 'Note updated successfully.' : 'Note created successfully.');
    }

    public function cancelEdit(): void
    {
        $this->showEditor = false;
        $this->resetEditor();
    }

    public function resetEditor(): void
    {
        $this->editingNoteId = null;
        $this->title = '';
        $this->content = '';
        $this->customerId = null;
        $this->isPinned = false;
        $this->resetErrorBag();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function deleteNote(): void
    {
        if ($this->deleteId) {
            Note::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('noteSaved');
            session()->flash('success', 'Note deleted successfully.');
        }
    }

    public function togglePin(int $id): void
    {
        $note = Note::findOrFail($id);
        $note->update(['is_pinned' => !$note->is_pinned]);
        $this->dispatch('noteSaved');
    }

    public function getCustomersProperty()
    {
        return Customer::orderBy('name')->get();
    }

    public function render()
    {
        $query = Note::query()->with(['customer', 'creator'])
            ->when($this->search, fn($q) => $q->where(function ($sub) {
                $sub->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            }))
            ->when($this->customerFilter, fn($q) => $q->where('customer_id', $this->customerFilter))
            ->orderBy('is_pinned', 'desc')
            ->latest();

        return view('livewire.notes.index', [
            'notes' => $query->paginate(12),
        ]);
    }
}
