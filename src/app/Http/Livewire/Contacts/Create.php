<?php

namespace App\Http\Livewire\Contacts;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'New Contact'])]
class Create extends Component
{
    public function render()
    {
        return view('livewire.contacts.create');
    }
}
