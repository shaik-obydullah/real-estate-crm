<?php

namespace App\Http\Livewire\Contacts;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'Edit Contact'])]
class Edit extends Component
{
    public function render()
    {
        return view('livewire.contacts.edit');
    }
}
