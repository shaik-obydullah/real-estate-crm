<?php

namespace App\Http\Livewire\Activities;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Activity;
use App\Models\User;
use App\Models\Customer;
use App\Models\Contact;
use App\Models\Opportunity;
use App\Models\Lead;

#[Layout('layouts.app', ['title' => 'Edit Activity'])]
class Edit extends Component
{
    public Activity $activity;

    public string $type = 'call';
    public string $title = '';
    public ?string $description = null;
    public ?string $date = null;
    public ?string $time = null;
    public ?int $duration = null;
    public ?string $outcome = null;
    public ?int $contact_id = null;
    public ?int $customer_id = null;
    public ?int $opportunity_id = null;
    public ?int $lead_id = null;
    public ?int $assigned_to = null;

    public function mount(Activity $activity)
    {
        $this->activity = $activity;
        $this->type = $activity->type;
        $this->title = $activity->title;
        $this->description = $activity->description;
        $this->date = $activity->date?->format('Y-m-d');
        $this->time = $activity->time ? substr((string) $activity->time, 0, 5) : null;
        $this->duration = $activity->duration;
        $this->outcome = $activity->outcome;
        $this->contact_id = $activity->contact_id;
        $this->customer_id = $activity->customer_id;
        $this->opportunity_id = $activity->opportunity_id;
        $this->lead_id = $activity->lead_id;
        $this->assigned_to = $activity->assigned_to;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:call,email,meeting,note,task,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|max:5000',
            'date' => 'required|date',
            'time' => 'nullable',
            'duration' => 'nullable|integer|min:0',
            'outcome' => 'nullable|max:1000',
            'contact_id' => 'nullable|exists:contacts,id',
            'customer_id' => 'nullable|exists:customers,id',
            'opportunity_id' => 'nullable|exists:opportunities,id',
            'lead_id' => 'nullable|exists:leads,id',
            'assigned_to' => 'nullable|exists:users,id',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->activity->update($this->only([
            'type', 'title', 'description', 'date', 'time', 'duration',
            'outcome', 'contact_id', 'customer_id', 'opportunity_id',
            'lead_id', 'assigned_to',
        ]));

        session()->flash('success', 'Activity updated successfully.');
        return redirect()->route('activities.index');
    }

    public function cancel()
    {
        return redirect()->route('activities.index');
    }

    public function getUsersProperty()
    {
        return User::where('is_active', true)->orderBy('name')->get();
    }

    public function getCustomersProperty()
    {
        return Customer::orderBy('name')->get();
    }

    public function getContactsProperty()
    {
        return Contact::orderBy('first_name')->get();
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
        return view('livewire.activities.edit');
    }
}
