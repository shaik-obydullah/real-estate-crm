<?php

namespace App\Http\Livewire\Activities;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'View Activity'])]
class Show extends Component
{
    public function render()
    {
        return view('livewire.activities.show');
    }
}
