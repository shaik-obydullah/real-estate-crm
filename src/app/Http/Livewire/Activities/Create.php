<?php

namespace App\Http\Livewire\Activities;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'New Activity'])]
class Create extends Component
{
    public function render()
    {
        return view('livewire.activities.create');
    }
}
