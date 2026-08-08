<?php

namespace App\Http\Livewire\Chat;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Str;

#[Layout('layouts.app', ['title' => 'Chat'])]
class Index extends Component
{
    public ?int $selectedContactId = null;
    public string $message = '';
    public string $search = '';
    public $contacts;
    public $messages = [];

    protected $listeners = ['messageSent' => '$refresh'];

    public function mount(): void
    {
        $this->loadContacts();
        if ($this->contacts->isNotEmpty() && !$this->selectedContactId) {
            $this->selectContact($this->contacts->first()->id);
        }
    }

    public function loadContacts(): void
    {
        $userId = auth()->id();
        $this->contacts = User::where('id', '!=', $userId)
            ->with(['sentMessages' => function ($q) use ($userId) {
                $q->where('receiver_id', $userId)->latest()->limit(1);
            }, 'receivedMessages' => function ($q) use ($userId) {
                $q->where('sender_id', $userId)->latest()->limit(1);
            }])
            ->get();

        if ($this->contacts->isEmpty()) {
            $this->contacts = User::where('id', '!=', $userId)->limit(20)->get();
        }
    }

    public function selectContact(int $id): void
    {
        $this->selectedContactId = $id;
        $this->loadMessages();

        ChatMessage::where('sender_id', $id)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function loadMessages(): void
    {
        if (!$this->selectedContactId) return;

        $userId = auth()->id();
        $this->messages = ChatMessage::query()
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $this->selectedContactId);
            })->orWhere(function ($q) use ($userId) {
                $q->where('sender_id', $this->selectedContactId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function sendMessage(): void
    {
        if (empty(trim($this->message)) || !$this->selectedContactId) return;

        ChatMessage::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->selectedContactId,
            'message' => trim($this->message),
            'is_read' => false,
        ]);

        $this->message = '';
        $this->loadMessages();
        $this->dispatch('messageSent');
    }

    public function getUnreadCount(int $userId): int
    {
        return ChatMessage::where('sender_id', $userId)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->count();
    }

    public function getLastMessagePreview(int $userId): string
    {
        $msg = ChatMessage::where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)->where('receiver_id', auth()->id());
        })->orWhere(function ($q) use ($userId) {
            $q->where('sender_id', auth()->id())->where('receiver_id', $userId);
        })->latest()->first();

        return $msg ? Str::limit($msg->message, 40) : 'No messages yet';
    }

    public function render()
    {
        $contacts = $this->contacts;
        if ($this->search) {
            $contacts = $contacts->filter(fn($c) => str_contains(strtolower($c->name), strtolower($this->search)));
        }

        return view('livewire.chat.index', [
            'chatContacts' => $contacts,
        ]);
    }
}
