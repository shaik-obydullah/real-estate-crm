<?php

namespace App\Http\Livewire\Payments;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Payment;

#[Layout('layouts.app', ['title' => 'View Payment'])]
class Show extends Component
{
    public Payment $payment;

    public function mount(Payment $payment)
    {
        $this->payment = $payment->load(['invoice', 'customer', 'creator']);
    }

    public function getStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-700',
            'completed' => 'bg-green-100 text-green-700',
            'failed' => 'bg-red-100 text-red-700',
            'refunded' => 'bg-gray-100 text-gray-500',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getMethodLabel(string $method): string
    {
        return match($method) {
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'credit_card' => 'Credit Card',
            'check' => 'Check',
            'other' => 'Other',
            default => ucfirst($method),
        };
    }

    public function render()
    {
        return view('livewire.payments.show');
    }
}
