<?php

namespace App\Http\Livewire\Invoices;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Invoice;

#[Layout('layouts.app', ['title' => 'View Invoice'])]
class Show extends Component
{
    public Invoice $invoice;

    public function mount(Invoice $invoice): void
    {
        $this->invoice = $invoice;
    }

    public function getStatusColor(string $status): string
    {
        return match ($status) {
            'draft' => 'bg-gray-100 text-gray-700',
            'sent' => 'bg-blue-100 text-blue-700',
            'paid' => 'bg-green-100 text-green-700',
            'partial' => 'bg-yellow-100 text-yellow-700',
            'overdue' => 'bg-red-100 text-red-700',
            'cancelled' => 'bg-gray-100 text-gray-500',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function render()
    {
        return view('livewire.invoices.show', [
            'invoice' => $this->invoice->load([
                'items.product', 'customer', 'salesOrder', 'payments', 'creator',
            ]),
        ]);
    }
}
