<?php

namespace App\Http\Livewire\Followups;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'Edit Follow-up'])]
class Edit extends Component
{
    public function render()
    {
        return view('livewire.followups.edit');
    }
}
