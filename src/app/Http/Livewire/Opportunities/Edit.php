<?php

namespace App\Http\Livewire\Opportunities;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\User;

#[Layout('layouts.app', ['title' => 'Edit Opportunity'])]
class Edit extends Component
{
    public ?Opportunity $opportunity = null;
    public string $name = '';
    public ?string $company_name = null;
    public ?int $contact_id = null;
    public ?int $lead_id = null;
    public float $value = 0;
    public string $stage = 'new';
    public int $probability = 10;
    public ?string $expected_closing_date = null;
    public ?int $assigned_to = null;
    public ?string $notes = null;

    public function mount(Opportunity $opportunity): void
    {
        $this->opportunity = $opportunity;
        $this->fill($opportunity->only([
            'name', 'company_name', 'contact_id', 'lead_id', 'value', 'stage',
            'probability', 'expected_closing_date', 'assigned_to', 'notes',
        ]));
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|max:255',
            'contact_id' => 'nullable|exists:contacts,id',
            'lead_id' => 'nullable|exists:leads,id',
            'value' => 'nullable|numeric|min:0',
            'stage' => 'required|in:new,qualified,meeting,proposal,negotiation,won,lost',
            'probability' => 'nullable|integer|min:0|max:100',
            'expected_closing_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|max:5000',
        ];
    }

    public function updatedStage(): void
    {
        $probabilities = [
            'new' => 10,
            'qualified' => 25,
            'meeting' => 40,
            'proposal' => 60,
            'negotiation' => 75,
            'won' => 100,
            'lost' => 0,
        ];

        $this->probability = $probabilities[$this->stage] ?? 0;
    }

    public function save()
    {
        $this->validate();

        $this->opportunity->update($this->only([
            'name', 'company_name', 'contact_id', 'lead_id', 'value', 'stage',
            'probability', 'expected_closing_date', 'assigned_to', 'notes',
        ]));

        session()->flash('success', 'Opportunity updated successfully.');
        return redirect()->route('opportunities.index');
    }

    public function cancel()
    {
        return redirect()->route('opportunities.index');
    }

    public function getContactsProperty()
    {
        return Contact::orderBy('last_name')->orderBy('first_name')->get();
    }

    public function getLeadsProperty()
    {
        return Lead::orderBy('title')->get();
    }

    public function getUsersProperty()
    {
        return User::where('is_active', true)->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.opportunities.edit');
    }
}
