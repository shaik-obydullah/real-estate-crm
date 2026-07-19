<?php

namespace App\Http\Livewire\Leads;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'Edit Lead'])]
class Edit extends Component
{
    public function render()
    {
        return view('livewire.leads.edit');
    }
}
