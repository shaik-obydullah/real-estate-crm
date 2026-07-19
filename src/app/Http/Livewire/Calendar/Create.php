<?php

namespace App\Http\Livewire\Calendar;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'New Event'])]
class Create extends Component
{
    public function render()
    {
        return view('livewire.calendar.create');
    }
}
