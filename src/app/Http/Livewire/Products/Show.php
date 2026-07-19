<?php

namespace App\Http\Livewire\Products;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'View Product'])]
class Show extends Component
{
    public function render()
    {
        return view('livewire.products.show');
    }
}
