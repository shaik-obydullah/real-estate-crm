<?php

namespace App\Http\Livewire\Followups;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Followup;

#[Layout('layouts.app', ['title' => 'Follow-up Details'])]
class Show extends Component
{
    public Followup $followup;

    public bool $confirmDelete = false;

    public function mount(Followup $followup)
    {
        $this->followup = $followup;
    }

    public function confirmDelete(): void
    {
        $this->confirmDelete = true;
    }

    public function delete()
    {
        $this->followup->delete();

        session()->flash('success', 'Follow-up deleted successfully.');
        return redirect()->route('followups.index');
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

    public function render()
    {
        return view('livewire.followups.show', [
            'followup' => $this->followup->load([
                'contact', 'customer', 'opportunity', 'lead', 'assignedTo',
            ]),
        ]);
    }
}
