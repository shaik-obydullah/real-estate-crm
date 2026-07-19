<?php

namespace App\Http\Livewire\Invoices;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'Edit Invoice'])]
class Edit extends Component
{
    public function render()
    {
        return view('livewire.invoices.edit');
    }
}
