<?php

namespace App\Http\Livewire\Invoices;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'View Invoice'])]
class Show extends Component
{
    public function render()
    {
        return view('livewire.invoices.show');
    }
}
