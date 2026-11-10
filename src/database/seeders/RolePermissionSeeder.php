<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('permissions.defaults') as $role => $permissions) {
            $ids = [];

            foreach ($permissions as $permission) {
                if ($permission === '*') {
                    $ids = Permission::pluck('id')->all();
                    break;
                }

                if ($id = Permission::where('name', $permission)->value('id')) {
                    $ids[] = $id;
                }
            }

            foreach (array_unique($ids) as $id) {
                DB::table('role_permissions')->updateOrInsert(
                    ['role' => $role, 'permission_id' => $id],
                    ['created_at' => now(), 'updated_at' => now()],
                );
            }
        }
    }
}
