<?php

namespace App\Http\Livewire\Calendar;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\CalendarEvent;
use Carbon\Carbon;

#[Layout('layouts.app', ['title' => 'Calendar'])]
class Index extends Component
{
    public string $currentMonth = '';
    public ?string $selectedDate = null;
    public string $typeFilter = '';

    protected $listeners = ['' => '$refresh'];

    public function mount(): void
    {
        $this->currentMonth = now()->format('Y-m');
    }

    public function previousMonth(): void
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->addMonth()->format('Y-m');
    }

    public function goToToday(): void
    {
        $this->currentMonth = now()->format('Y-m');
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $this->selectedDate === $date ? null : $date;
    }

    public function getMonthNameProperty(): string
    {
        return Carbon::parse($this->currentMonth)->format('F Y');
    }

    public function getDaysProperty(): array
    {
        $start = Carbon::parse($this->currentMonth)->startOfMonth();
        $end = Carbon::parse($this->currentMonth)->endOfMonth();

        $days = [];
        $currentDay = $start->copy()->startOfWeek(Carbon::MONDAY);

        while ($currentDay->lte($end->copy()->endOfWeek(Carbon::SUNDAY))) {
            $days[] = [
                'date' => $currentDay->format('Y-m-d'),
                'day' => $currentDay->day,
                'isCurrentMonth' => $currentDay->month === $start->month,
                'isToday' => $currentDay->isToday(),
                'isWeekend' => $currentDay->isSaturday() || $currentDay->isSunday(),
            ];
            $currentDay->addDay();
        }

        return $days;
    }

    public function getEventsProperty()
    {
        $start = Carbon::parse($this->currentMonth)->startOfMonth()->startOfDay();
        $end = Carbon::parse($this->currentMonth)->endOfMonth()->endOfDay();

        return CalendarEvent::with(["customer", "contact"])
            ->whereBetween('start_time', [$start, $end])
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn($e) => $e->start_time->format('Y-m-d'));
    }

    public function getSelectedDayEventsProperty()
    {
        if (! $this->selectedDate) {
            return collect();
        }

        return CalendarEvent::with(["customer", "contact"])
            ->whereDate('start_time', $this->selectedDate)
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->orderBy('start_time')
            ->get();
    }

    public function getTypeColor(string $type): string
    {
        return match($type) {
            'meeting' => 'bg-purple-500',
            'call' => 'bg-blue-500',
            'task' => 'bg-yellow-500',
            default => 'bg-gray-400',
        };
    }

    public function getTypeTextColor(string $type): string
    {
        return match($type) {
            'meeting' => 'text-purple-700 bg-purple-100',
            'call' => 'text-blue-700 bg-blue-100',
            'task' => 'text-yellow-700 bg-yellow-100',
            default => 'text-gray-700 bg-gray-100',
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
        return view('livewire.calendar.index');
    }
}
