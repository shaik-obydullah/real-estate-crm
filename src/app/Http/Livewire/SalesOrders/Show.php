<?php

namespace App\Http\Livewire\SalesOrders;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'View Sales Order'])]
class Show extends Component
{
    public function render()
    {
        return view('livewire.sales-orders.show');
    }
}
