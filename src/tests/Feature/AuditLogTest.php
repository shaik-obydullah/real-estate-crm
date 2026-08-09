<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected function seedPermissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_creating_a_customer_records_an_audit_log(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        Customer::create(['name' => 'Audit Corp', 'type' => 'company']);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'create',
            'entity_type' => Customer::class,
            'entity_id' => 1,
        ]);
    }

    public function test_updating_and_deleting_customer_records_audit_logs(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Old Name', 'type' => 'company']);

        $this->actingAs($admin);

        $customer->update(['name' => 'New Name']);
        $customer->delete();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'update',
            'entity_type' => Customer::class,
            'entity_id' => $customer->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'delete',
            'entity_type' => Customer::class,
            'entity_id' => $customer->id,
        ]);

        $updateLog = AuditLog::where('action', 'update')->first();

        $this->assertSame('Old Name', $updateLog->old_values['name']);
        $this->assertSame('New Name', $updateLog->new_values['name']);
    }

    public function test_audit_logs_index_page_renders_for_admin(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/audit-logs')->assertOk();
    }
}
