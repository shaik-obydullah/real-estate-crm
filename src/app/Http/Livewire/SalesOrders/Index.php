<?php

namespace App\Http\Livewire\SalesOrders;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\SalesOrder;

#[Layout('layouts.app', ['title' => 'Sales Orders'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public array $selected = [];
    public bool $selectAll = false;
    public bool $bulkDelete = false;
    public ?int $deleteId = null;

    protected string $paginationTheme = 'tailwind';

    protected $listeners = ['salesOrderDeleted' => '$refresh'];

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

    public function deleteSalesOrder(): void
    {
        if ($this->deleteId) {
            SalesOrder::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('salesOrderDeleted');
            session()->flash('success', 'Sales order deleted successfully.');
        }
    }

    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selected = $this->salesOrdersQuery()->pluck('id')->toArray();
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
            SalesOrder::whereIn('id', $this->selected)->delete();
            $this->selected = [];
            $this->selectAll = false;
            $this->bulkDelete = false;
            $this->dispatch('salesOrderDeleted');
            session()->flash('success', $count . ' sales order(s) deleted successfully.');
        }
    }

    public function getStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-700',
            'confirmed' => 'bg-blue-100 text-blue-700',
            'processing' => 'bg-purple-100 text-purple-700',
            'shipped' => 'bg-indigo-100 text-indigo-700',
            'delivered' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    private function salesOrdersQuery()
    {
        return SalesOrder::query()->with(['customer'])
            ->when($this->search, fn($q) => $q->where(function ($sub) {
                $sub->where('order_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', fn($c) => $c->where('name', 'like', '%' . $this->search . '%'));
            }))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    public function render()
    {
        return view('livewire.sales-orders.index', [
            'salesOrders' => $this->salesOrdersQuery()->paginate(15),
            'totalOrders' => SalesOrder::count(),
        ]);
    }
}
