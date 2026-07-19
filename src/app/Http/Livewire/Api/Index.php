<?php

namespace App\Http\Livewire\Api;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\ApiKey;
use Illuminate\Support\Str;

#[Layout('layouts.app', ['title' => 'API Keys'])]
class Index extends Component
{
    public bool $showModal = false;
    public string $formName = '';
    public array $formPermissions = ['read'];
    public string $newKeyShown = '';

    public string $search = '';

    protected $listeners = ['closeModal'];

    public function closeModal()
    {
        $this->showModal = false;
        $this->formName = '';
        $this->formPermissions = ['read'];
    }

    public function togglePermission(string $perm)
    {
        if (in_array($perm, $this->formPermissions)) {
            $this->formPermissions = array_filter($this->formPermissions, fn($p) => $p !== $perm);
        } else {
            $this->formPermissions[] = $perm;
        }
    }

    public function generateKey()
    {
        $this->validate([
            'formName' => 'required|string|max:255',
            'formPermissions' => 'required|array|min:1',
        ]);

        $key = 'sk_' . Str::random(40);

        ApiKey::create([
            'user_id' => auth()->id(),
            'name' => $this->formName,
            'key' => $key,
            'permissions' => $this->formPermissions,
            'status' => 'active',
        ]);

        $this->newKeyShown = $key;
        session()->flash('success', 'API key generated. Copy it now - it will not be shown again.');
        $this->showModal = false;
        $this->formName = '';
        $this->formPermissions = ['read'];
    }

    public function revokeKey(int $id)
    {
        ApiKey::where('id', $id)->update(['status' => 'revoked']);
        session()->flash('success', 'API key revoked.');
    }

    public function deleteKey(int $id)
    {
        ApiKey::where('id', $id)->delete();
        session()->flash('success', 'API key deleted.');
    }

    public function render()
    {
        $apiKeys = ApiKey::with('user')
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->latest()
            ->get();

        return view('livewire.api.index', compact('apiKeys'));
    }
}
