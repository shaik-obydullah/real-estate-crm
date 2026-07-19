<?php

namespace App\Http\Livewire\Invoices;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'New Invoice'])]
class Create extends Component
{
    public function render()
    {
        return view('livewire.invoices.create');
    }
}
