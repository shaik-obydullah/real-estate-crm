<?php

namespace App\Http\Livewire\Opportunities;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'New Opportunity'])]
class Create extends Component
{
    public function render()
    {
        return view('livewire.opportunities.create');
    }
}
