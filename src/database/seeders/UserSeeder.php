<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@crm.com',
            'password' => Hash::make('password'),
            'department' => 'Management',
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@crm.com',
            'password' => Hash::make('password'),
            'department' => 'Sales',
            'role' => 'manager',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'John Doe',
            'email' => 'john@crm.com',
            'password' => Hash::make('password'),
            'department' => 'Sales',
            'role' => 'sales',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Mike Johnson',
            'email' => 'mike@crm.com',
            'password' => Hash::make('password'),
            'department' => 'Sales',
            'role' => 'sales',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Sarah Wilson',
            'email' => 'sarah@crm.com',
            'password' => Hash::make('password'),
            'department' => 'Support',
            'role' => 'support',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
