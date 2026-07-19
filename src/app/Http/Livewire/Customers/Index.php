<?php

namespace App\Http\Livewire\Customers;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Customer;
use App\Models\User;

#[Layout('layouts.app', ['title' => 'Customers'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $typeFilter = '';
    public string $accountManagerFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public array $selected = [];
    public ?int $deleteId = null;
    public bool $selectAll = false;

    protected string $paginationTheme = 'tailwind';

    protected $listeners = ['customerDeleted' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingAccountManagerFilter(): void
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
            $this->selected = Customer::pluck('id')->toArray();
        }
    }

    public function updatedSelected(): void
    {
        $this->selectAll = false;
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function deleteCustomer(): void
    {
        if ($this->deleteId) {
            Customer::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('customerDeleted');
            session()->flash('success', 'Customer deleted successfully.');
        }
    }

    public function getAccountManagersProperty()
    {
        return User::where('is_active', true)->orderBy('name')->get();
    }

    public function render()
    {
        $query = Customer::query()
            ->with(['accountManager', 'tags'])
            ->when($this->search, fn($q) => $q->where(function ($sub) {
                $sub->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            }))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->when($this->accountManagerFilter, fn($q) => $q->where('account_manager_id', $this->accountManagerFilter))
            ->orderBy($this->sortBy, $this->sortDirection);

        return view('livewire.customers.index', [
            'customers' => $query->paginate(10),
            'totalCustomers' => Customer::count(),
        ]);
    }
}
