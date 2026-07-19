<?php

namespace App\Http\Livewire\Tasks;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'Edit Task'])]
class Edit extends Component
{
    public function render()
    {
        return view('livewire.tasks.edit');
    }
}
