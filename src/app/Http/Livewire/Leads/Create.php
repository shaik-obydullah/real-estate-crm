<?php

namespace App\Http\Livewire\Leads;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Lead;
use App\Models\User;

#[Layout('layouts.app', ['title' => 'New Lead'])]
class Create extends Component
{
    public string $title = '';
    public ?string $company_name = null;
    public ?string $contact_name = null;
    public ?string $contact_email = null;
    public ?string $contact_phone = null;
    public string $source = 'website';
    public string $status = 'new';
    public string $priority = 'medium';
    public float $value = 0;
    public ?string $expected_closing_date = null;
    public ?int $assigned_to = null;
    public ?string $notes = null;

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'company_name' => 'nullable|max:255',
            'contact_name' => 'nullable|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|max:255',
            'source' => 'nullable|max:255',
            'status' => 'required|in:new,contacted,qualified,proposal,negotiation,converted,lost',
            'priority' => 'required|in:low,medium,high,urgent',
            'value' => 'nullable|numeric|min:0',
            'expected_closing_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|max:2000',
        ];
    }

    public function save()
    {
        $this->validate();

        Lead::create($this->only([
            'title', 'company_name', 'contact_name', 'contact_email', 'contact_phone',
            'source', 'status', 'priority', 'value', 'expected_closing_date',
            'assigned_to', 'notes',
        ]));

        session()->flash('success', 'Lead created successfully.');
        return redirect()->route('leads.index');
    }

    public function cancel()
    {
        return redirect()->route('leads.index');
    }

    public function getSalesPeopleProperty()
    {
        return User::where('is_active', true)->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.leads.create');
    }
}
