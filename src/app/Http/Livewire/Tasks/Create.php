<?php

namespace App\Http\Livewire\Tasks;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Task;
use App\Models\User;
use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\Lead;

#[Layout('layouts.app', ['title' => 'New Task'])]
class Create extends Component
{
    public string $title = '';
    public ?string $description = null;
    public string $priority = 'medium';
    public string $status = 'pending';
    public ?string $due_date = null;
    public ?string $due_time = null;
    public ?int $assigned_to = null;
    public ?int $related_customer_id = null;
    public ?int $related_opportunity_id = null;
    public ?int $related_lead_id = null;

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|max:5000',
            'priority' => 'required|in:high,medium,low',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable',
            'assigned_to' => 'nullable|exists:users,id',
            'related_customer_id' => 'nullable|exists:customers,id',
            'related_opportunity_id' => 'nullable|exists:opportunities,id',
            'related_lead_id' => 'nullable|exists:leads,id',
        ];
    }

    public function save()
    {
        $this->validate();

        Task::create($this->only([
            'title', 'description', 'priority', 'status', 'due_date',
            'due_time', 'assigned_to', 'related_customer_id',
            'related_opportunity_id', 'related_lead_id',
        ]));

        session()->flash('success', 'Task created successfully.');
        return redirect()->route('tasks.index');
    }

    public function cancel()
    {
        return redirect()->route('tasks.index');
    }

    public function getUsersProperty()
    {
        return User::where('is_active', true)->orderBy('name')->get();
    }

    public function getCustomersProperty()
    {
        return Customer::orderBy('name')->get();
    }

    public function getOpportunitiesProperty()
    {
        return Opportunity::orderBy('name')->get();
    }

    public function getLeadsProperty()
    {
        return Lead::orderBy('title')->get();
    }

    public function render()
    {
        return view('livewire.tasks.create');
    }
}
