<?php

namespace App\Http\Livewire\Contacts;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Contact;
use App\Models\Customer;

#[Layout('layouts.app', ['title' => 'Edit Contact'])]
class Edit extends Component
{
    public ?Contact $contact = null;
    public ?int $customer_id = null;
    public string $first_name = '';
    public string $last_name = '';
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $whatsapp = null;
    public ?string $position = null;
    public ?string $department = null;
    public ?string $birthday = null;
    public bool $is_primary = false;
    public ?string $notes = null;

    public function mount(Contact $contact): void
    {
        $this->contact = $contact;
        $this->fill($contact->only([
            'customer_id', 'first_name', 'last_name', 'email', 'phone',
            'whatsapp', 'position', 'department', 'birthday', 'is_primary', 'notes',
        ]));
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|max:255',
            'whatsapp' => 'nullable|max:255',
            'position' => 'nullable|max:255',
            'department' => 'nullable|max:255',
            'birthday' => 'nullable|date',
            'is_primary' => 'boolean',
            'notes' => 'nullable|max:2000',
        ];
    }

    public function updatedCustomerId(): void
    {
        if (! $this->customer_id) {
            return;
        }

        $this->is_primary = ! Contact::where('customer_id', $this->customer_id)
            ->where('id', '!=', $this->contact?->id)
            ->where('is_primary', true)
            ->exists();
    }

    public function save()
    {
        $this->validate();

        $isPrimary = (bool) $this->is_primary;

        if ($isPrimary) {
            Contact::where('customer_id', $this->customer_id)
                ->where('id', '!=', $this->contact->id)
                ->update(['is_primary' => false]);
        } elseif (! Contact::where('customer_id', $this->customer_id)
            ->where('id', '!=', $this->contact->id)
            ->where('is_primary', true)
            ->exists()) {
            $isPrimary = true;
        }

        $this->contact->update([
            'customer_id' => $this->customer_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'position' => $this->position,
            'department' => $this->department,
            'birthday' => $this->birthday,
            'is_primary' => $isPrimary,
            'notes' => $this->notes,
        ]);

        session()->flash('success', 'Contact updated successfully.');
        return redirect()->route('contacts.index');
    }

    public function cancel()
    {
        return redirect()->route('contacts.index');
    }

    public function getCustomersProperty()
    {
        return Customer::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.contacts.edit');
    }
}
