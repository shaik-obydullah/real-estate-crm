<?php

namespace App\Http\Livewire\Calendar;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\CalendarEvent;

#[Layout('layouts.app', ['title' => 'View Event'])]
class Show extends Component
{
    public CalendarEvent $event;

    public function mount(CalendarEvent $event)
    {
        $this->event = $event;
    }

    public function getTypeBadge(string $type): string
    {
        return match($type) {
            'meeting' => 'bg-purple-100 text-purple-700',
            'call' => 'bg-blue-100 text-blue-700',
            'task' => 'bg-yellow-100 text-yellow-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getTypeIcon(string $type): string
    {
        return match($type) {
            'meeting' => 'fas fa-users',
            'call' => 'fas fa-phone',
            'task' => 'fas fa-check-circle',
            default => 'fas fa-calendar',
        };
    }

    public function render()
    {
        return view('livewire.calendar.show', [
            'event' => $this->event->load(['user', 'contact', 'customer', 'opportunity']),
        ]);
    }
}
