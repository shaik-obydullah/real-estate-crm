<?php

namespace App\Http\Livewire\Opportunities;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'Edit Opportunity'])]
class Edit extends Component
{
    public function render()
    {
        return view('livewire.opportunities.edit');
    }
}
