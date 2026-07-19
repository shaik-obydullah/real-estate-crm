<?php

namespace App\Http\Livewire\Calendar;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'View Event'])]
class Show extends Component
{
    public function render()
    {
        return view('livewire.calendar.show');
    }
}
