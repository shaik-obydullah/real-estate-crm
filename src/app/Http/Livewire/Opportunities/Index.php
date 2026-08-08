<?php

namespace App\Http\Livewire\Opportunities;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Opportunity;
use App\Models\User;

#[Layout('layouts.app', ['title' => 'Opportunities'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $stageFilter = '';
    public string $assignedFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public array $selected = [];
    public bool $selectAll = false;

    protected string $paginationTheme = 'tailwind';

    protected $listeners = ['opportunityDeleted' => '$refresh'];

    protected $stageConfig = [
        'new' => ['label' => 'New', 'color' => 'blue', 'bg' => 'bg-blue-100 text-blue-700'],
        'qualified' => ['label' => 'Qualified', 'color' => 'indigo', 'bg' => 'bg-indigo-100 text-indigo-700'],
        'meeting' => ['label' => 'Meeting', 'color' => 'yellow', 'bg' => 'bg-yellow-100 text-yellow-700'],
        'proposal' => ['label' => 'Proposal', 'color' => 'orange', 'bg' => 'bg-orange-100 text-orange-700'],
        'negotiation' => ['label' => 'Negotiation', 'color' => 'red', 'bg' => 'bg-red-100 text-red-700'],
        'won' => ['label' => 'Won', 'color' => 'green', 'bg' => 'bg-green-100 text-green-700'],
        'lost' => ['label' => 'Lost', 'color' => 'gray', 'bg' => 'bg-gray-100 text-gray-700'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStageFilter(): void
    {
        $this->resetPage();
    }

    public function updatingAssignedFilter(): void
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
            $this->selected = Opportunity::pluck('id')->toArray();
        }
    }

    public function deleteOpportunity(int $id): void
    {
        Opportunity::findOrFail($id)->delete();
        $this->dispatch('opportunityDeleted');
        session()->flash('success', 'Opportunity deleted successfully.');
    }

    public function getStageBadgeClass(string $stage): string
    {
        return $this->stageConfig[$stage]['bg'] ?? 'bg-gray-100 text-gray-700';
    }

    public function render()
    {
        $query = Opportunity::query()
            ->with(['contact', 'lead', 'assignedTo'])
            ->when($this->search, fn($q) => $q->where(function ($sub) {
                $sub->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('company_name', 'like', '%' . $this->search . '%');
            }))
            ->when($this->stageFilter, fn($q) => $q->where('stage', $this->stageFilter))
            ->when($this->assignedFilter, fn($q) => $q->where('assigned_to', $this->assignedFilter))
            ->orderBy($this->sortBy, $this->sortDirection);

        return view('livewire.opportunities.index', [
            'opportunities' => $query->paginate(10),
            'totalOpportunities' => Opportunity::count(),
            'users' => User::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
