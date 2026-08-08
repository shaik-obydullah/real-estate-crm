<?php

namespace Tests\Feature;

use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function seedPermissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_from_protected_pages(): void
    {
        $this->get('/customers')->assertRedirect('/login');
        $this->get('/users')->assertRedirect('/login');
    }

    public function test_admin_can_access_anything(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/customers')->assertOk();
        $this->actingAs($admin)->get('/users')->assertOk();
        $this->actingAs($admin)->get('/settings')->assertOk();
        $this->actingAs($admin)->get('/roles')->assertOk();
        $this->actingAs($admin)->get('/api')->assertOk();
    }

    public function test_sales_role_can_access_customers_but_not_users(): void
    {
        $this->seedPermissions();

        $sales = User::factory()->create(['role' => 'sales']);

        $this->actingAs($sales)->get('/customers')->assertOk();
        $this->actingAs($sales)->get('/users')->assertForbidden();
        $this->actingAs($sales)->get('/roles')->assertForbidden();
        $this->actingAs($sales)->get('/settings')->assertForbidden();
    }

    public function test_support_role_can_access_tickets_but_not_customers_create(): void
    {
        $this->seedPermissions();

        $support = User::factory()->create(['role' => 'support']);

        $this->actingAs($support)->get('/tickets')->assertOk();
        $this->actingAs($support)->get('/customers')->assertOk();
        $this->actingAs($support)->get('/customers/create')->assertForbidden();
        $this->actingAs($support)->get('/reports')->assertForbidden();
    }

    public function test_manager_can_access_reports_but_not_admin_tools(): void
    {
        $this->seedPermissions();

        $manager = User::factory()->create(['role' => 'manager']);

        $this->actingAs($manager)->get('/reports')->assertOk();
        $this->actingAs($manager)->get('/audit-logs')->assertForbidden();
        $this->actingAs($manager)->get('/api')->assertForbidden();
    }
}
