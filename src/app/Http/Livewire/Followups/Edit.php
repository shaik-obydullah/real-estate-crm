<?php

namespace App\Http\Livewire\Followups;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Followup;
use App\Models\User;
use App\Models\Customer;
use App\Models\Contact;
use App\Models\Opportunity;
use App\Models\Lead;

#[Layout('layouts.app', ['title' => 'Edit Follow-up'])]
class Edit extends Component
{
    public Followup $followup;

    public string $type = 'call';
    public string $title = '';
    public ?string $description = null;
    public ?string $due_date = null;
    public ?string $due_time = null;
    public string $priority = 'medium';
    public string $status = 'pending';
    public ?int $contact_id = null;
    public ?int $customer_id = null;
    public ?int $opportunity_id = null;
    public ?int $lead_id = null;
    public ?int $assigned_to = null;
    public ?string $reminder_at = null;

    public function mount(Followup $followup)
    {
        $this->followup = $followup;
        $this->type = $followup->type;
        $this->title = $followup->title;
        $this->description = $followup->description;
        $this->due_date = $followup->due_date?->format('Y-m-d');
        $this->due_time = $followup->due_time ? substr((string) $followup->due_time, 0, 5) : null;
        $this->priority = $followup->priority;
        $this->status = $followup->status;
        $this->contact_id = $followup->contact_id;
        $this->customer_id = $followup->customer_id;
        $this->opportunity_id = $followup->opportunity_id;
        $this->lead_id = $followup->lead_id;
        $this->assigned_to = $followup->assigned_to;
        $this->reminder_at = $followup->reminder_at?->format('Y-m-d\TH:i');
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:call,email,meeting,follow_up,reminder',
            'title' => 'required|string|max:255',
            'description' => 'nullable|max:5000',
            'due_date' => 'required|date',
            'due_time' => 'nullable',
            'priority' => 'required|in:high,medium,low',
            'status' => 'required|in:pending,completed,cancelled,overdue',
            'contact_id' => 'nullable|exists:contacts,id',
            'customer_id' => 'nullable|exists:customers,id',
            'opportunity_id' => 'nullable|exists:opportunities,id',
            'lead_id' => 'nullable|exists:leads,id',
            'assigned_to' => 'nullable|exists:users,id',
            'reminder_at' => 'nullable',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->followup->update($this->only([
            'type', 'title', 'description', 'due_date', 'due_time',
            'priority', 'status', 'contact_id', 'customer_id',
            'opportunity_id', 'lead_id', 'assigned_to', 'reminder_at',
        ]));

        session()->flash('success', 'Follow-up updated successfully.');
        return redirect()->route('followups.index');
    }

    public function cancel()
    {
        return redirect()->route('followups.index');
    }

    public function getUsersProperty()
    {
        return User::where('is_active', true)->orderBy('name')->get();
    }

    public function getContactsProperty()
    {
        return Contact::orderBy('first_name')->get();
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
        return view('livewire.followups.edit');
    }
}
