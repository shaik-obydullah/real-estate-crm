<?php

namespace App\Http\Livewire\Activities;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'Edit Activity'])]
class Edit extends Component
{
    public function render()
    {
        return view('livewire.activities.edit');
    }
}
