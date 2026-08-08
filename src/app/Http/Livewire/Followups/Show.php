<?php

namespace App\Http\Livewire\Followups;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'View Follow-up'])]
class Show extends Component
{
    public function render()
    {
        return view('livewire.followups.show');
    }
}
