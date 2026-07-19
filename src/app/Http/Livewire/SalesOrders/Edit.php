<?php

namespace App\Http\Livewire\SalesOrders;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'Edit Sales Order'])]
class Edit extends Component
{
    public function render()
    {
        return view('livewire.sales-orders.edit');
    }
}
