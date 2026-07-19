<?php

namespace App\Http\Livewire\Notifications;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Notification;

#[Layout('layouts.app', ['title' => 'Notifications'])]
class Index extends Component
{
    use WithPagination;

    public string $filter = 'all';

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function markAsRead(int $id)
    {
        Notification::where('id', $id)->update(['read_at' => now()]);
    }

    public function markAllAsRead()
    {
        Notification::whereNull('read_at')->update(['read_at' => now()]);
        session()->flash('success', 'All notifications marked as read.');
    }

    public function deleteNotification(int $id)
    {
        Notification::where('id', $id)->delete();
    }

    public function render()
    {
        $query = Notification::query();

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->latest()->paginate(20);
        $unreadCount = Notification::whereNull('read_at')->count();

        return view('livewire.notifications.index', compact('notifications', 'unreadCount'));
    }
}
