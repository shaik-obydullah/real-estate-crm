<?php

namespace App\Http\Livewire\Invoices;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Invoice;

#[Layout('layouts.app', ['title' => 'Invoices'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public ?int $deleteId = null;

    protected string $paginationTheme = 'tailwind';

    protected $listeners = ['' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
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

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function deleteInvoice(): void
    {
        if ($this->deleteId) {
            Invoice::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('invoiceDeleted');
            session()->flash('success', 'Invoice deleted successfully.');
        }
    }

    public function getStatusColor(string $status): string
    {
        return match($status) {
            'draft' => 'bg-gray-100 text-gray-700',
            'sent' => 'bg-blue-100 text-blue-700',
            'paid' => 'bg-green-100 text-green-700',
            'partial' => 'bg-yellow-100 text-yellow-700',
            'overdue' => 'bg-red-100 text-red-700',
            'cancelled' => 'bg-gray-100 text-gray-500',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function render()
    {
        $query = Invoice::query()->with(['customer'])
            ->when($this->search, fn($q) => $q->where(function ($sub) {
                $sub->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', fn($c) => $c->where('name', 'like', '%' . $this->search . '%'));
            }))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortBy, $this->sortDirection);

        return view('livewire.invoices.index', [
            'invoices' => $query->paginate(15),
            'totalInvoices' => Invoice::count(),
        ]);
    }
}
