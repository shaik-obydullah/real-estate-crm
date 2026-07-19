<?php

namespace App\Http\Livewire\Products;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Product;

#[Layout('layouts.app', ['title' => 'Products'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoryFilter = '';
    public string $statusFilter = '';
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';
    public ?int $deleteId = null;

    protected string $paginationTheme = 'tailwind';

    protected $listeners = ['' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
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

    public function deleteProduct(): void
    {
        if ($this->deleteId) {
            Product::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('productDeleted');
            session()->flash('success', 'Product deleted successfully.');
        }
    }

    public function getStatusColor(string $status): string
    {
        return match($status) {
            'active' => 'bg-green-100 text-green-700',
            'inactive' => 'bg-gray-100 text-gray-700',
            'discontinued' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getStockColor(int $stock): string
    {
        if ($stock > 10) return 'text-green-600';
        if ($stock >= 5) return 'text-yellow-600';
        return 'text-red-600';
    }

    public function getCategories()
    {
        return Product::distinct()->whereNotNull('category')->pluck('category')->sort()->values();
    }

    public function render()
    {
        $query = Product::query()
            ->when($this->search, fn($q) => $q->where(function ($sub) {
                $sub->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            }))
            ->when($this->categoryFilter, fn($q) => $q->where('category', $this->categoryFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortBy, $this->sortDirection);

        return view('livewire.products.index', [
            'products' => $query->paginate(15),
            'totalProducts' => Product::count(),
            'categories' => $this->getCategories(),
        ]);
    }
}
