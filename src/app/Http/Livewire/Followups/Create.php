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

#[Layout('layouts.app', ['title' => 'New Follow-up'])]
class Create extends Component
{
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

        Followup::create($this->only([
            'type', 'title', 'description', 'due_date', 'due_time',
            'priority', 'status', 'contact_id', 'customer_id',
            'opportunity_id', 'lead_id', 'assigned_to', 'reminder_at',
        ]));

        session()->flash('success', 'Follow-up created successfully.');
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
        return view('livewire.followups.create');
    }
}
