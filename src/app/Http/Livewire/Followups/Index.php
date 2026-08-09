<?php

namespace App\Http\Livewire\Followups;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Followup;

#[Layout('layouts.app', ['title' => 'Follow-ups'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $typeFilter = '';
    public string $priorityFilter = '';
    public string $statusFilter = '';
    public string $sortBy = 'due_date';
    public string $sortDirection = 'asc';
    public ?int $deleteId = null;
    public array $selected = [];
    public bool $selectAll = false;
    public bool $bulkDelete = false;

    protected string $paginationTheme = 'tailwind';

    protected $listeners = ['followupDeleted' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter(): void
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

    public function deleteFollowup(): void
    {
        if ($this->deleteId) {
            Followup::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('followupDeleted');
            session()->flash('success', 'Follow-up deleted successfully.');
        }
    }

    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selected = $this->followupsQuery()->pluck('id')->toArray();
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
            Followup::whereIn('id', $this->selected)->delete();
            $this->selected = [];
            $this->selectAll = false;
            $this->bulkDelete = false;
            $this->dispatch('followupDeleted');
            session()->flash('success', $count . ' follow-up(s) deleted successfully.');
        }
    }

    public function getStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-gray-100 text-gray-500',
            'overdue' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getPriorityColor(string $priority): string
    {
        return match($priority) {
            'high' => 'bg-red-100 text-red-700',
            'medium' => 'bg-yellow-100 text-yellow-700',
            'low' => 'bg-green-100 text-green-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getTypeIcon(string $type): string
    {
        return match($type) {
            'call' => 'fas fa-phone',
            'email' => 'fas fa-envelope',
            'meeting' => 'fas fa-users',
            'follow_up' => 'fas fa-redo',
            'reminder' => 'fas fa-bell',
            default => 'fas fa-clock',
        };
    }

    public function isOverdue(Followup $followup): bool
    {
        return $followup->due_date
            && $followup->due_date->isPast()
            && $followup->status === 'pending';
    }

    private function followupsQuery()
    {
        return Followup::query()->with(['customer', 'lead', 'assignedTo'])
            ->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->when($this->priorityFilter, fn($q) => $q->where('priority', $this->priorityFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    public function render()
    {
        return view('livewire.followups.index', [
            'followups' => $this->followupsQuery()->paginate(15),
            'totalFollowups' => Followup::count(),
        ]);
    }
}
