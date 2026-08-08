<?php

namespace App\Http\Livewire\Timeline;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Activity;
use App\Models\Task;
use App\Models\Followup;
use App\Models\Note;
use App\Models\Customer;

#[Layout('layouts.app', ['title' => 'Timeline'])]
class Index extends Component
{
    public ?int $customerId = null;
    public array $events = [];

    public function mount(): void
    {
        $this->loadTimeline();
    }

    public function updatedCustomerId(): void
    {
        $this->loadTimeline();
    }

    public function loadTimeline(): void
    {
        $events = collect();

        $activitiesQuery = Activity::with(['customer', 'contact', 'assignedTo', 'creator']);
        $tasksQuery = Task::with(['customer', 'assignedTo']);
        $followupsQuery = Followup::with(['customer', 'contact', 'assignedTo']);
        $notesQuery = Note::with(['customer', 'creator']);

        if ($this->customerId) {
            $activitiesQuery->where('customer_id', $this->customerId);
            $tasksQuery->where('related_customer_id', $this->customerId);
            $followupsQuery->where('customer_id', $this->customerId);
            $notesQuery->where('customer_id', $this->customerId);
        }

        $activities = $activitiesQuery->latest('date')->limit(25)->get()->map(fn($a) => (object)[
            'type' => 'activity',
            'event_type' => $a->type ?? 'Meeting',
            'title' => $a->title,
            'description' => $a->description,
            'date' => $a->date ?? $a->created_at,
            'customer' => $a->customer?->name,
            'person' => $a->assignedTo?->name ?? $a->creator?->name,
            'icon' => $this->getActivityIcon($a->type),
            'color' => $this->getActivityColor($a->type),
        ]);

        $tasks = $tasksQuery->latest()->limit(25)->get()->map(fn($t) => (object)[
            'type' => 'task',
            'event_type' => 'Task',
            'title' => $t->title,
            'description' => $t->description,
            'date' => $t->due_date ?? $t->created_at,
            'customer' => $t->customer?->name,
            'person' => $t->assignedTo?->name,
            'icon' => 'fa-check-square',
            'color' => 'yellow',
            'status' => $t->status,
        ]);

        $followups = $followupsQuery->latest()->limit(25)->get()->map(fn($f) => (object)[
            'type' => 'followup',
            'event_type' => $f->type ?? 'Follow-up',
            'title' => $f->title,
            'description' => $f->description,
            'date' => $f->due_date ?? $f->created_at,
            'customer' => $f->customer?->name,
            'person' => $f->assignedTo?->name,
            'icon' => $this->getFollowupIcon($f->type),
            'color' => $this->getFollowupColor($f->type),
            'status' => $f->status,
        ]);

        $notes = $notesQuery->latest()->limit(25)->get()->map(fn($n) => (object)[
            'type' => 'note',
            'event_type' => 'Note',
            'title' => $n->title,
            'description' => $n->content,
            'date' => $n->created_at,
            'customer' => $n->customer?->name,
            'person' => $n->creator?->name,
            'icon' => 'fa-sticky-note',
            'color' => 'gray',
        ]);

        $this->events = $activities->concat($tasks)->concat($followups)->concat($notes)
            ->sortByDesc(fn($item) => $item->date)
            ->take(50)
            ->values()
            ->toArray();
    }

    public function getActivityIcon(?string $type): string
    {
        return match(strtolower($type ?? '')) {
            'call' => 'fa-phone',
            'email' => 'fa-envelope',
            'meeting' => 'fa-users',
            'proposal' => 'fa-file-invoice-dollar',
            'invoice' => 'fa-file-invoice',
            'created' => 'fa-plus-circle',
            default => 'fa-clipboard-list',
        };
    }

    public function getActivityColor(?string $type): string
    {
        return match(strtolower($type ?? '')) {
            'call' => 'indigo',
            'email' => 'green',
            'meeting' => 'purple',
            'proposal' => 'blue',
            'invoice' => 'green',
            'created' => 'gray',
            default => 'blue',
        };
    }

    public function getFollowupIcon(?string $type): string
    {
        return match(strtolower($type ?? '')) {
            'call' => 'fa-phone',
            'email' => 'fa-envelope',
            'meeting' => 'fa-calendar',
            'task' => 'fa-check-square',
            default => 'fa-phone-volume',
        };
    }

    public function getFollowupColor(?string $type): string
    {
        return match(strtolower($type ?? '')) {
            'call' => 'indigo',
            'email' => 'green',
            'meeting' => 'purple',
            'task' => 'yellow',
            default => 'blue',
        };
    }

    public function getCustomersProperty()
    {
        return Customer::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.timeline.index');
    }
}
