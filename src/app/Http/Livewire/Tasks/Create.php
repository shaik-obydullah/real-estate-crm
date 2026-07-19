<?php

namespace App\Http\Livewire\Tasks;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'New Task'])]
class Create extends Component
{
    public function render()
    {
        return view('livewire.tasks.create');
    }
}
