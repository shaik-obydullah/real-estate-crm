<?php

namespace App\Http\Livewire\Activities;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Activity;

#[Layout('layouts.app', ['title' => 'View Activity'])]
class Show extends Component
{
    public Activity $activity;

    public function mount(Activity $activity)
    {
        $this->activity = $activity;
    }

    public function getTypeBadge(string $type): string
    {
        return match($type) {
            'call' => 'bg-blue-100 text-blue-700',
            'email' => 'bg-green-100 text-green-700',
            'meeting' => 'bg-purple-100 text-purple-700',
            'note' => 'bg-yellow-100 text-yellow-700',
            'task' => 'bg-indigo-100 text-indigo-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function render()
    {
        return view('livewire.activities.show', [
            'activity' => $this->activity->load([
                'contact', 'customer', 'opportunity', 'lead', 'assignedTo', 'creator',
            ]),
        ]);
    }
}
