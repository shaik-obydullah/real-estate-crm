<?php

namespace App\Http\Livewire\Activities;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Activity;

#[Layout('layouts.app', ['title' => 'Activities'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $typeFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $sortBy = 'date';
    public string $sortDirection = 'desc';
    public ?int $deleteId = null;

    protected string $paginationTheme = 'tailwind';

    protected $listeners = ['' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
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

    public function deleteActivity(): void
    {
        if ($this->deleteId) {
            Activity::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('activityDeleted');
            session()->flash('success', 'Activity deleted successfully.');
        }
    }

    public function getTypeIcon(string $type): string
    {
        return match($type) {
            'call' => 'fas fa-phone',
            'email' => 'fas fa-envelope',
            'meeting' => 'fas fa-users',
            'note' => 'fas fa-sticky-note',
            'task' => 'fas fa-check-circle',
            default => 'fas fa-circle',
        };
    }

    public function getTypeColor(string $type): string
    {
        return match($type) {
            'call' => 'bg-blue-100 text-blue-700',
            'email' => 'bg-green-100 text-green-700',
            'meeting' => 'bg-purple-100 text-purple-700',
            'note' => 'bg-yellow-100 text-yellow-700',
            'task' => 'bg-indigo-100 text-indigo-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getTypeBgColor(string $type): string
    {
        return match($type) {
            'call' => 'text-blue-600',
            'email' => 'text-green-600',
            'meeting' => 'text-purple-600',
            'note' => 'text-yellow-600',
            'task' => 'text-indigo-600',
            default => 'text-gray-600',
        };
    }

    public function render()
    {
        $query = Activity::query()->with(['customer', 'lead', 'assignedTo', 'contact'])
            ->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->when($this->dateFrom, fn($q) => $q->where('date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->where('date', '<=', $this->dateTo))
            ->orderBy($this->sortBy, $this->sortDirection);

        return view('livewire.activities.index', [
            'activities' => $query->paginate(15),
            'totalActivities' => Activity::count(),
        ]);
    }
}
