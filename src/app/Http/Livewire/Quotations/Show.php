<?php

namespace App\Http\Livewire\Quotations;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'View Quotation'])]
class Show extends Component
{
    public function render()
    {
        return view('livewire.quotations.show');
    }
}
