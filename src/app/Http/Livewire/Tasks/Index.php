<?php

namespace App\Http\Livewire\Tasks;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Task;

#[Layout('layouts.app', ['title' => 'Tasks'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $priorityFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public ?int $deleteId = null;
    public array $selected = [];
    public bool $selectAll = false;
    public bool $bulkDelete = false;

    protected string $paginationTheme = 'tailwind';

    protected $listeners = ['taskDeleted' => '$refresh'];

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

    public function deleteTask(): void
    {
        if ($this->deleteId) {
            Task::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('taskDeleted');
            session()->flash('success', 'Task deleted successfully.');
        }
    }

    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selected = $this->tasksQuery()->pluck('id')->toArray();
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
            Task::whereIn('id', $this->selected)->delete();
            $this->selected = [];
            $this->selectAll = false;
            $this->bulkDelete = false;
            $this->dispatch('taskDeleted');
            session()->flash('success', $count . ' task(s) deleted successfully.');
        }
    }

    public function getStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-700',
            'in_progress' => 'bg-blue-100 text-blue-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-gray-100 text-gray-500',
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

    public function getPriorityIcon(string $priority): string
    {
        return match($priority) {
            'high' => 'fas fa-arrow-up',
            'medium' => 'fas fa-minus',
            'low' => 'fas fa-arrow-down',
            default => 'fas fa-minus',
        };
    }

    private function tasksQuery()
    {
        return Task::query()->with(['assignedTo', 'customer', 'lead'])
            ->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->priorityFilter, fn($q) => $q->where('priority', $this->priorityFilter))
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    public function render()
    {
        return view('livewire.tasks.index', [
            'tasks' => $this->tasksQuery()->paginate(15),
            'totalTasks' => Task::count(),
        ]);
    }
}
