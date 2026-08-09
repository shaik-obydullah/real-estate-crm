<?php

namespace App\Http\Livewire\Tasks;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Task;

#[Layout('layouts.app', ['title' => 'Task Details'])]
class Show extends Component
{
    public Task $task;
    public bool $confirmingDelete = false;

    public function mount(Task $task)
    {
        $this->task = $task;
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

    public function confirmDelete(): void
    {
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        $this->task->delete();
        session()->flash('success', 'Task deleted successfully.');
        return redirect()->route('tasks.index');
    }

    public function render()
    {
        return view('livewire.tasks.show', [
            'task' => $this->task->load([
                'assignedTo', 'customer', 'opportunity', 'lead', 'tags',
            ]),
        ]);
    }
}
