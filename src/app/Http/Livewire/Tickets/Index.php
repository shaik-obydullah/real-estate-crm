<?php

namespace App\Http\Livewire\Tickets;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Customer;

#[Layout('layouts.app', ['title' => 'Tickets'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $priorityFilter = '';
    public string $assignedFilter = '';

    public bool $showModal = false;
    public int $editId = 0;
    public ?int $deleteId = null;
    public string $formSubject = '';
    public string $formDescription = '';
    public string $formPriority = 'medium';
    public string $formStatus = 'open';
    public int $formCustomer = 0;
    public int $formAssignedTo = 0;

    protected $listeners = ['closeModal'];

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    public function resetForm()
    {
        $this->editId = 0;
        $this->formSubject = '';
        $this->formDescription = '';
        $this->formPriority = 'medium';
        $this->formStatus = 'open';
        $this->formCustomer = 0;
        $this->formAssignedTo = 0;
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id)
    {
        $ticket = Ticket::findOrFail($id);
        $this->editId = $id;
        $this->formSubject = $ticket->subject;
        $this->formDescription = $ticket->description ?? '';
        $this->formPriority = $ticket->priority;
        $this->formStatus = $ticket->status;
        $this->formCustomer = $ticket->customer_id;
        $this->formAssignedTo = $ticket->assigned_to;
        $this->showModal = true;
    }

    public function confirmDelete(int $id)
    {
        $this->deleteId = $id;
    }

    public function save()
    {
        $this->validate([
            'formSubject' => 'required|string|max:255',
            'formDescription' => 'required|string|max:5000',
            'formPriority' => 'required|in:low,medium,high,urgent',
            'formStatus' => 'required|in:open,in_progress,waiting,resolved,closed',
        ]);

        if ($this->editId) {
            Ticket::findOrFail($this->editId)->update([
                'subject' => $this->formSubject,
                'description' => $this->formDescription,
                'priority' => $this->formPriority,
                'status' => $this->formStatus,
                'customer_id' => $this->formCustomer ?: null,
                'assigned_to' => $this->formAssignedTo ?: null,
            ]);
            session()->flash('success', 'Ticket updated.');
        } else {
            Ticket::create([
                'ticket_number' => 'TK-' . str_pad(Ticket::max('id') + 1, 4, '0', STR_PAD_LEFT),
                'subject' => $this->formSubject,
                'description' => $this->formDescription,
                'priority' => $this->formPriority,
                'status' => $this->formStatus,
                'customer_id' => $this->formCustomer ?: null,
                'assigned_to' => $this->formAssignedTo ?: null,
                'created_by' => auth()->id(),
            ]);
            session()->flash('success', 'Ticket created.');
        }

        $this->closeModal();
    }

    public function deleteTicket(int $id)
    {
        Ticket::where('id', $id)->delete();
        $this->deleteId = null;
        session()->flash('success', 'Ticket deleted.');
    }

    public function updatedSearch() { $this->resetPage(); }
    public function updatedStatusFilter() { $this->resetPage(); }
    public function updatedPriorityFilter() { $this->resetPage(); }
    public function updatedAssignedFilter() { $this->resetPage(); }

    public function getStatusColor(string $status): string
    {
        return match ($status) {
            'open' => 'bg-blue-100 text-blue-700',
            'in_progress' => 'bg-yellow-100 text-yellow-700',
            'waiting' => 'bg-purple-100 text-purple-700',
            'resolved' => 'bg-green-100 text-green-700',
            'closed' => 'bg-gray-100 text-gray-500',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getPriorityColor(string $priority): string
    {
        return match ($priority) {
            'urgent' => 'bg-red-100 text-red-700',
            'high' => 'bg-orange-100 text-orange-700',
            'medium' => 'bg-blue-100 text-blue-700',
            'low' => 'bg-gray-100 text-gray-600',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function render()
    {
        $tickets = Ticket::query()
            ->with(['customer', 'assignedTo', 'creator'])
            ->when($this->search, fn($q) => $q->where(function ($q2) {
                $q2->where('subject', 'like', '%' . $this->search . '%')
                    ->orWhere('ticket_number', 'like', '%' . $this->search . '%');
            }))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->priorityFilter, fn($q) => $q->where('priority', $this->priorityFilter))
            ->when($this->assignedFilter, fn($q) => $q->where('assigned_to', $this->assignedFilter))
            ->latest()
            ->paginate(15);

        $users = User::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('livewire.tickets.index', compact('tickets', 'users', 'customers'));
    }
}
