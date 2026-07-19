<?php

namespace App\Http\Livewire\Contacts;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Contact;
use App\Models\Customer;

#[Layout('layouts.app', ['title' => 'Contacts'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $companyFilter = '';
    public string $departmentFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public array $selected = [];
    public bool $selectAll = false;
    public ?int $deleteId = null;

    protected string $paginationTheme = 'tailwind';

    protected $listeners = ['contactDeleted' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCompanyFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDepartmentFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selected = [];
        } else {
            $this->selected = Contact::pluck('id')->toArray();
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function deleteContact(): void
    {
        if ($this->deleteId) {
            Contact::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('contactDeleted');
            session()->flash('success', 'Contact deleted successfully.');
        }
    }

    public function getAvatarColorProperty(): array
    {
        $colors = [
            'bg-red-100 text-red-700',
            'bg-blue-100 text-blue-700',
            'bg-green-100 text-green-700',
            'bg-yellow-100 text-yellow-700',
            'bg-purple-100 text-purple-700',
            'bg-pink-100 text-pink-700',
            'bg-indigo-100 text-indigo-700',
            'bg-teal-100 text-teal-700',
        ];
        return $colors;
    }

    public function render()
    {
        $query = Contact::query()
            ->with(['customer'])
            ->when($this->search, fn($q) => $q->where(function ($sub) {
                $sub->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            }))
            ->when($this->companyFilter, fn($q) => $q->where('customer_id', $this->companyFilter))
            ->when($this->departmentFilter, fn($q) => $q->where('department', $this->departmentFilter))
            ->orderBy($this->sortBy, $this->sortDirection);

        return view('livewire.contacts.index', [
            'contacts' => $query->paginate(10),
            'totalContacts' => Contact::count(),
            'companies' => Customer::orderBy('name')->get(),
            'departments' => Contact::whereNotNull('department')->distinct()->pluck('department'),
        ]);
    }
}
