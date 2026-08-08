<?php

namespace App\Http\Livewire\AuditLogs;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\AuditLog;
use App\Models\User;

#[Layout('layouts.app', ['title' => 'Audit Logs'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $userFilter = '';
    public string $actionFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    public function updatedSearch() { $this->resetPage(); }
    public function updatedUserFilter() { $this->resetPage(); }
    public function updatedActionFilter() { $this->resetPage(); }
    public function updatedDateFrom() { $this->resetPage(); }
    public function updatedDateTo() { $this->resetPage(); }

    public function render()
    {
        $logs = AuditLog::with('user')
            ->when($this->search, fn($q) => $q->where(function ($q2) {
                $q2->where('entity_type', 'like', '%' . $this->search . '%')
                    ->orWhere('action', 'like', '%' . $this->search . '%')
                    ->orWhere('ip_address', 'like', '%' . $this->search . '%');
            }))
            ->when($this->userFilter, fn($q) => $q->where('user_id', $this->userFilter))
            ->when($this->actionFilter, fn($q) => $q->where('action', $this->actionFilter))
            ->when($this->dateFrom, fn($q) => $q->where('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->where('created_at', '<=', $this->dateTo . ' 23:59:59'))
            ->latest()
            ->paginate(20);

        $users = User::orderBy('name')->get();
        $actions = AuditLog::distinct()->pluck('action')->filter();

        return view('livewire.audit-logs.index', compact('logs', 'users', 'actions'));
    }
}
