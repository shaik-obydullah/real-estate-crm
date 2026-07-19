<?php

namespace App\Http\Livewire\Tasks;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'View Task'])]
class Show extends Component
{
    public function render()
    {
        return view('livewire.tasks.show');
    }
}
