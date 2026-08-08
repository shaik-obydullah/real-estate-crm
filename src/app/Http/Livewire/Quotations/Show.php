<?php

namespace App\Http\Livewire\Quotations;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Quotation;

#[Layout('layouts.app', ['title' => 'View Quotation'])]
class Show extends Component
{
    public Quotation $quotation;

    public function mount(Quotation $quotation)
    {
        $this->quotation = $quotation->load(['items', 'customer', 'opportunity', 'creator']);
    }

    public function getStatusColor(string $status): string
    {
        return match($status) {
            'draft' => 'bg-gray-100 text-gray-700',
            'sent' => 'bg-blue-100 text-blue-700',
            'accepted' => 'bg-green-100 text-green-700',
            'rejected' => 'bg-red-100 text-red-700',
            'expired' => 'bg-yellow-100 text-yellow-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function render()
    {
        return view('livewire.quotations.show');
    }
}
