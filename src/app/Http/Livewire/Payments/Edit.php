<?php

namespace App\Http\Livewire\Payments;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'Edit Payment'])]
class Edit extends Component
{
    public function render()
    {
        return view('livewire.payments.edit');
    }
}
