<?php

namespace App\Http\Livewire\SalesOrders;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\SalesOrder;

#[Layout('layouts.app', ['title' => 'View Sales Order'])]
class Show extends Component
{
    public SalesOrder $salesOrder;

    public function mount(SalesOrder $salesOrder): void
    {
        $this->salesOrder = $salesOrder->load(['items.product', 'customer', 'quotation', 'creator']);
    }

    public function getStatusColor(string $status): string
    {
        return match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-700',
            'confirmed' => 'bg-blue-100 text-blue-700',
            'processing' => 'bg-purple-100 text-purple-700',
            'shipped' => 'bg-indigo-100 text-indigo-700',
            'delivered' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function render()
    {
        return view('livewire.sales-orders.show');
    }
}
