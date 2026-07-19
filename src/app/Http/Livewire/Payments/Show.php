<?php

namespace App\Http\Livewire\Payments;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'View Payment'])]
class Show extends Component
{
    public function render()
    {
        return view('livewire.payments.show');
    }
}
