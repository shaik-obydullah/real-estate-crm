<?php

namespace App\Http\Livewire\SalesOrders;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'New Sales Order'])]
class Create extends Component
{
    public function render()
    {
        return view('livewire.sales-orders.create');
    }
}
