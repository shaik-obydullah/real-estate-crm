<?php

namespace App\Http\Livewire\Calendar;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'Edit Event'])]
class Edit extends Component
{
    public function render()
    {
        return view('livewire.calendar.edit');
    }
}
