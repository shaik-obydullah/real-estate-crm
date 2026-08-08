<?php

namespace App\Http\Livewire\Users;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\User;

#[Layout('layouts.app', ['title' => 'Users'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public string $statusFilter = '';
    public string $departmentFilter = '';

    public bool $showModal = false;
    public int $editId = 0;
    public string $formName = '';
    public string $formEmail = '';
    public string $formPassword = '';
    public string $formPhone = '';
    public string $formRole = 'sales';
    public string $formDepartment = '';
    public bool $formIsActive = true;

    protected $listeners = ['closeModal'];

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    public function resetForm()
    {
        $this->editId = 0;
        $this->formName = '';
        $this->formEmail = '';
        $this->formPassword = '';
        $this->formPhone = '';
        $this->formRole = 'sales';
        $this->formDepartment = '';
        $this->formIsActive = true;
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id)
    {
        $user = User::findOrFail($id);
        $this->editId = $id;
        $this->formName = $user->name;
        $this->formEmail = $user->email;
        $this->formPhone = $user->phone ?? '';
        $this->formRole = $user->role ?? 'sales';
        $this->formDepartment = $user->department ?? '';
        $this->formIsActive = $user->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'formName' => 'required|string|max:255',
            'formEmail' => 'required|email|max:255',
            'formRole' => 'required|in:admin,manager,sales,support',
        ]);

        if ($this->editId) {
            $user = User::findOrFail($this->editId);
            $user->update([
                'name' => $this->formName,
                'email' => $this->formEmail,
                'phone' => $this->formPhone,
                'role' => $this->formRole,
                'department' => $this->formDepartment,
                'is_active' => $this->formIsActive,
            ]);
            if ($this->formPassword) {
                $user->update(['password' => $this->formPassword]);
            }
        } else {
            User::create([
                'name' => $this->formName,
                'email' => $this->formEmail,
                'password' => $this->formPassword ?: 'password',
                'phone' => $this->formPhone,
                'role' => $this->formRole,
                'department' => $this->formDepartment,
                'is_active' => $this->formIsActive,
            ]);
        }

        session()->flash('success', 'User saved successfully.');
        $this->closeModal();
    }

    public function toggleStatus(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        session()->flash('success', 'User status updated.');
    }

    public function deleteUser(int $id)
    {
        User::where('id', $id)->delete();
        session()->flash('success', 'User deleted.');
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where(function ($q2) {
                $q2->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            }))
            ->when($this->roleFilter, fn($q) => $q->where('role', $this->roleFilter))
            ->when($this->statusFilter, fn($q) => $q->where('is_active', $this->statusFilter === 'active'))
            ->when($this->departmentFilter, fn($q) => $q->where('department', $this->departmentFilter))
            ->latest()
            ->paginate(15);

        $departments = User::distinct()->whereNotNull('department')->pluck('department');

        return view('livewire.users.index', compact('users', 'departments'));
    }
}
