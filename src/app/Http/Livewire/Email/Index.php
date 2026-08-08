<?php

namespace App\Http\Livewire\Email;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;

#[Layout('layouts.app', ["title" => "Email"])]
class Index extends Component
{
    public bool $composeMode = false;
    public ?int $selectedEmailId = null;
    public string $selectedFolder = 'inbox';
    public string $to = '';
    public string $subject = '';
    public string $body = '';
    public string $search = '';
    public ?array $selectedEmail = null;
    public array $filteredEmails = [];
    public array $emails = [];
    public array $folders = [];

    public function mount(): void
    {
        $this->loadEmails();
        $this->buildFolders();
    }

    public function buildFolders(): void
    {
        $inbox = collect($this->emails)->where('folder', 'inbox');
        $sent = collect($this->emails)->where('folder', 'sent');
        $drafts = collect($this->emails)->where('folder', 'drafts');
        $spam = collect($this->emails)->where('folder', 'spam');
        $trash = collect($this->emails)->where('folder', 'trash');

        $this->folders = [
            ['name' => 'inbox', 'label' => 'Inbox', 'icon' => 'fa-inbox', 'count' => $inbox->where('is_read', false)->count(), 'color' => 'blue'],
            ['name' => 'sent', 'label' => 'Sent', 'icon' => 'fa-paper-plane', 'count' => 0, 'color' => 'green'],
            ['name' => 'drafts', 'label' => 'Drafts', 'icon' => 'fa-file-alt', 'count' => $drafts->count(), 'color' => 'yellow'],
            ['name' => 'spam', 'label' => 'Spam', 'icon' => 'fa-exclamation-circle', 'count' => $spam->count(), 'color' => 'red'],
            ['name' => 'trash', 'label' => 'Trash', 'icon' => 'fa-trash', 'count' => 0, 'color' => 'gray'],
        ];
    }

    public function loadEmails(): void
    {
        $this->emails = [
            ['id' => 1, 'from' => 'Sarah Johnson', 'email' => 'sarah@example.com', 'subject' => 'Property Viewing Confirmation', 'preview' => 'Hi, I would like to confirm the property viewing scheduled for tomorrow at 2 PM...', 'body' => "Hi,\n\nI would like to confirm the property viewing scheduled for tomorrow at 2 PM for the waterfront villa at 45 Ocean Drive.\n\nPlease let me know if you need any additional information.\n\nBest regards,\nSarah Johnson", 'date' => '2026-07-18 10:30:00', 'is_read' => false, 'folder' => 'inbox', 'avatar_color' => 'purple'],
            ['id' => 2, 'from' => 'Michael Chen', 'email' => 'michael@realty.com', 'subject' => 'Mortgage Pre-Approval Documents', 'preview' => 'Please find attached the mortgage pre-approval documents for your review...', 'body' => "Hello,\n\nPlease find attached the mortgage pre-approval documents for your review. The bank has approved up to $750,000.\n\nLet me know if you have any questions.\n\nCheers,\nMichael Chen", 'date' => '2026-07-17 16:45:00', 'is_read' => false, 'folder' => 'inbox', 'avatar_color' => 'blue'],
            ['id' => 3, 'from' => 'Emily Davis', 'email' => 'emily@design.co', 'subject' => 'Interior Design Proposal', 'preview' => 'Here is the interior design proposal we discussed during our last meeting...', 'body' => "Hi there,\n\nHere is the interior design proposal we discussed during our last meeting.\n\nBest,\nEmily Davis", 'date' => '2026-07-17 09:15:00', 'is_read' => true, 'folder' => 'inbox', 'avatar_color' => 'pink'],
            ['id' => 4, 'from' => 'Robert Wilson', 'email' => 'robert@law.com', 'subject' => 'Contract Review Complete', 'preview' => 'I have completed the review of the purchase agreement...', 'body' => "Dear Client,\n\nI have completed the review of the purchase agreement.\n\nRegards,\nRobert Wilson, Esq.", 'date' => '2026-07-16 14:20:00', 'is_read' => true, 'folder' => 'inbox', 'avatar_color' => 'green'],
            ['id' => 5, 'from' => 'You', 'email' => 'admin@crm.com', 'subject' => 'RE: Property Viewing Confirmation', 'preview' => 'Thank you Sarah, the viewing is confirmed...', 'body' => "Thank you Sarah,\n\nThe viewing is confirmed for tomorrow at 2 PM.\n\nBest regards", 'date' => '2026-07-18 11:00:00', 'is_read' => true, 'folder' => 'sent', 'avatar_color' => 'blue'],
        ];
    }

    public function selectFolder(string $folder): void
    {
        $this->selectedFolder = $folder;
        $this->selectedEmailId = null;
    }

    public function selectEmail(int $id): void
    {
        $this->selectedEmailId = $id;
        $this->composeMode = false;
        $key = array_search($id, array_column($this->emails, 'id'));
        if ($key !== false) {
            $this->emails[$key]['is_read'] = true;
        }
    }

    public function toggleCompose(): void
    {
        $this->composeMode = !$this->composeMode;
        if ($this->composeMode) {
            $this->selectedEmailId = null;
            $this->to = '';
            $this->subject = '';
            $this->body = '';
        }
    }

    public function sendEmail(): void
    {
        if (empty(trim($this->to)) || empty(trim($this->subject))) return;

        $this->emails[] = [
            'id' => count($this->emails) + 1,
            'from' => 'You',
            'email' => auth()->user()->email ?? 'admin@crm.com',
            'subject' => $this->subject,
            'preview' => Str::limit($this->body, 80),
            'body' => $this->body,
            'date' => now()->format('Y-m-d H:i:s'),
            'is_read' => true,
            'folder' => 'sent',
            'avatar_color' => 'blue',
        ];

        $this->composeMode = false;
        $this->to = '';
        $this->subject = '';
        $this->body = '';
        $this->buildFolders();
        session()->flash('success', 'Email sent successfully.');
    }

    public function deleteEmail(int $id): void
    {
        $this->emails = array_filter($this->emails, fn($e) => $e['id'] !== $id);
        $this->emails = array_values($this->emails);
        $this->selectedEmailId = null;
        $this->buildFolders();
    }

    public function render()
    {
        $filtered = collect($this->emails)->where('folder', $this->selectedFolder);
        if ($this->search) {
            $search = strtolower($this->search);
            $filtered = $filtered->filter(fn($e) =>
                str_contains(strtolower($e['subject']), $search) ||
                str_contains(strtolower($e['from']), $search) ||
                str_contains(strtolower($e['preview']), $search)
            );
        }
        $this->filteredEmails = $filtered->sortByDesc('date')->values()->toArray();
        $this->selectedEmail = collect($this->emails)->firstWhere('id', $this->selectedEmailId);

        return view('livewire.email.index');
    }
}
