<?php

namespace App\Http\Livewire\Followups;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'New Follow-up'])]
class Create extends Component
{
    public function render()
    {
        return view('livewire.followups.create');
    }
}
