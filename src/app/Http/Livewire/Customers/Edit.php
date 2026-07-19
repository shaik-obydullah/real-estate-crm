<?php

namespace App\Http\Livewire\Customers;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\User;

#[Layout('layouts.app', ['title' => 'Edit Customer'])]
class Edit extends Component
{
    public ?Customer $customer = null;
    public string $name = '';
    public string $type = 'individual';
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $industry = null;
    public ?string $website = null;
    public ?string $address = null;
    public ?string $city = null;
    public ?string $state = null;
    public ?string $country = null;
    public ?string $postal_code = null;
    public float $credit_limit = 0;
    public string $status = 'active';
    public ?string $notes = null;
    public ?int $account_manager_id = null;

    public function mount(Customer $customer): void
    {
        $this->customer = $customer;
        $this->fill($customer->only([
            'name', 'type', 'email', 'phone', 'industry', 'website',
            'address', 'city', 'state', 'country', 'postal_code',
            'credit_limit', 'status', 'notes', 'account_manager_id',
        ]));
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:individual,company',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|max:255',
            'industry' => 'nullable|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|max:1000',
            'city' => 'nullable|max:255',
            'state' => 'nullable|max:255',
            'country' => 'nullable|max:255',
            'postal_code' => 'nullable|max:20',
            'credit_limit' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,archived',
            'notes' => 'nullable|max:2000',
            'account_manager_id' => 'nullable|exists:users,id',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->customer->update($this->only([
            'name', 'type', 'email', 'phone', 'industry', 'website',
            'address', 'city', 'state', 'country', 'postal_code',
            'credit_limit', 'status', 'notes', 'account_manager_id',
        ]));

        session()->flash('success', 'Customer updated successfully.');
        return redirect()->route('customers.index');
    }

    public function cancel()
    {
        return redirect()->route('customers.index');
    }

    public function getAccountManagersProperty()
    {
        return User::where('is_active', true)->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.customers.edit');
    }
}
