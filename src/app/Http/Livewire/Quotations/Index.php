<?php

namespace App\Http\Livewire\Quotations;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Quotation;

#[Layout('layouts.app', ['title' => 'Quotations'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public array $selected = [];
    public bool $selectAll = false;
    public bool $bulkDelete = false;
    public ?int $deleteId = null;

    protected string $paginationTheme = 'tailwind';

    protected $listeners = ['quotationDeleted' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
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

    public function deleteQuotation(): void
    {
        if ($this->deleteId) {
            Quotation::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('quotationDeleted');
            session()->flash('success', 'Quotation deleted successfully.');
        }
    }

    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selected = $this->quotationsQuery()->pluck('id')->toArray();
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
            Quotation::whereIn('id', $this->selected)->delete();
            $this->selected = [];
            $this->selectAll = false;
            $this->bulkDelete = false;
            $this->dispatch('quotationDeleted');
            session()->flash('success', $count . ' quotation(s) deleted successfully.');
        }
    }

    public function getStatusColor(string $status): string
    {
        return match($status) {
            'draft' => 'bg-gray-100 text-gray-700',
            'sent' => 'bg-blue-100 text-blue-700',
            'accepted' => 'bg-green-100 text-green-700',
            'rejected' => 'bg-red-100 text-red-700',
            'expired' => 'bg-yellow-100 text-yellow-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    private function quotationsQuery()
    {
        return Quotation::query()->with(['customer'])
            ->when($this->search, fn($q) => $q->where(function ($sub) {
                $sub->where('quote_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', fn($c) => $c->where('name', 'like', '%' . $this->search . '%'));
            }))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->dateFrom, fn($q) => $q->where('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->where('created_at', '<=', $this->dateTo . ' 23:59:59'))
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    public function render()
    {
        return view('livewire.quotations.index', [
            'quotations' => $this->quotationsQuery()->paginate(15),
            'totalQuotations' => Quotation::count(),
        ]);
    }
}
