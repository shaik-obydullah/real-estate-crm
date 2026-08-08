<?php

namespace App\Http\Livewire\Roles;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app', ['title' => 'Roles & Permissions'])]
class Index extends Component
{
    public array $groups = [];
    public array $matrix = [];
    public string $activeRole = 'admin';

    public function mount()
    {
        $this->groups = config('permissions.groups');
        $this->loadMatrix();
    }

    public function loadMatrix()
    {
        foreach (config('permissions.roles') as $role) {
            $granted = DB::table('role_permissions')->where('role', $role)->pluck('permission_id');
            $names = Permission::whereIn('id', $granted)->pluck('name')->all();

            foreach ($this->groups as $perms) {
                foreach ($perms as $permission) {
                    $this->matrix[$role][$permission] = in_array($permission, $names);
                }
            }
        }
    }

    public function toggle(string $role, string $permission)
    {
        $this->matrix[$role][$permission] = ! $this->matrix[$role][$permission];
    }

    public function toggleGroup(string $role, string $group, bool $enable)
    {
        foreach ($this->groups[$group] as $permission) {
            $this->matrix[$role][$permission] = $enable;
        }
    }

    public function saveRole(string $role)
    {
        $ids = [];

        foreach ($this->groups as $perms) {
            foreach ($perms as $permission) {
                if (! empty($this->matrix[$role][$permission])) {
                    $ids[] = Permission::where('name', $permission)->value('id');
                }
            }
        }

        DB::table('role_permissions')->where('role', $role)->delete();

        $rows = collect($ids)->unique()->filter()->map(fn ($id) => [
            'role' => $role,
            'permission_id' => $id,
            'created_at' => now(),
            'updated_at' => now(),
        ])->all();

        DB::table('role_permissions')->insert($rows);

        session()->flash('success', ucfirst($role) . ' role permissions updated.');
    }

    public function render()
    {
        return view('livewire.roles.index', [
            'roles' => config('permissions.roles'),
            'roleLabels' => config('permissions.role_labels'),
        ]);
    }
}
