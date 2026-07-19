<?php

namespace App\Http\Livewire\Quotations;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'Edit Quotation'])]
class Edit extends Component
{
    public function render()
    {
        return view('livewire.quotations.edit');
    }
}
