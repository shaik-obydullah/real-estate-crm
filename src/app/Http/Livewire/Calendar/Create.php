<?php

namespace App\Http\Livewire\Calendar;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\CalendarEvent;
use App\Models\User;
use App\Models\Customer;
use App\Models\Contact;
use App\Models\Opportunity;

#[Layout('layouts.app', ['title' => 'New Event'])]
class Create extends Component
{
    public string $title = '';
    public ?string $description = null;
    public ?string $start_time = null;
    public ?string $end_time = null;
    public ?string $location = null;
    public string $type = 'meeting';
    public ?int $user_id = null;
    public ?int $contact_id = null;
    public ?int $customer_id = null;
    public ?int $opportunity_id = null;

    public function mount(): void
    {
        $this->user_id = auth()->id();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|max:5000',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'nullable|max:255',
            'type' => 'required|in:meeting,call,task,other',
            'user_id' => 'required|exists:users,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'customer_id' => 'nullable|exists:customers,id',
            'opportunity_id' => 'nullable|exists:opportunities,id',
        ];
    }

    public function save()
    {
        $this->validate();

        CalendarEvent::create($this->only([
            'title', 'description', 'start_time', 'end_time', 'location',
            'type', 'user_id', 'contact_id', 'customer_id', 'opportunity_id',
        ]));

        session()->flash('success', 'Calendar event created successfully.');
        return redirect()->route('calendar.index');
    }

    public function cancel()
    {
        return redirect()->route('calendar.index');
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

    public function render()
    {
        return view('livewire.calendar.create');
    }
}
