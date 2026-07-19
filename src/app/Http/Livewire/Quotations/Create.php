<?php

namespace App\Http\Livewire\Quotations;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'New Quotation'])]
class Create extends Component
{
    public function render()
    {
        return view('livewire.quotations.create');
    }
}
