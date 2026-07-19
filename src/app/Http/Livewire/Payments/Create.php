<?php

namespace App\Http\Livewire\Payments;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'New Payment'])]
class Create extends Component
{
    public function render()
    {
        return view('livewire.payments.create');
    }
}
