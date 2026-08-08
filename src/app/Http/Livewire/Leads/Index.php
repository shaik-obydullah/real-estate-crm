<?php

namespace App\Http\Livewire\Leads;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Lead;
use App\Models\User;

#[Layout('layouts.app', ['title' => 'Leads'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $priorityFilter = '';
    public string $sourceFilter = '';
    public string $salesPersonFilter = '';
    public string $viewMode = 'table';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public array $selected = [];
    public bool $selectAll = false;
    public ?int $deleteId = null;

    protected string $paginationTheme = 'tailwind';

    protected $listeners = ['leadDeleted' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSourceFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSalesPersonFilter(): void
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
            $this->selected = Lead::pluck('id')->toArray();
        }
    }

    public function toggleViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function deleteLead(): void
    {
        if ($this->deleteId) {
            Lead::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('leadDeleted');
            session()->flash('success', 'Lead deleted successfully.');
        }
    }

    public function getSalesPeopleProperty()
    {
        return User::where('is_active', true)->orderBy('name')->get();
    }

    public function getPipelineValueProperty()
    {
        return Lead::whereNotIn('status', ['lost'])->sum('value');
    }

    public function render()
    {
        $query = Lead::query()
            ->with(['assignedTo', 'tags'])
            ->when($this->search, fn($q) => $q->where(function ($sub) {
                $sub->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('company_name', 'like', '%' . $this->search . '%')
                    ->orWhere('contact_name', 'like', '%' . $this->search . '%');
            }))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->priorityFilter, fn($q) => $q->where('priority', $this->priorityFilter))
            ->when($this->sourceFilter, fn($q) => $q->where('source', $this->sourceFilter))
            ->when($this->salesPersonFilter, fn($q) => $q->where('assigned_to', $this->salesPersonFilter))
            ->orderBy($this->sortBy, $this->sortDirection);

        return view('livewire.leads.index', [
            'leads' => $query->paginate(10),
            'totalLeads' => Lead::count(),
        ]);
    }
}
