<?php

namespace Tests\Feature;

use App\Http\Livewire\Activities\Create as ActivitiesCreate;
use App\Http\Livewire\Activities\Edit as ActivitiesEdit;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    protected function seedPermissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_from_activity_create(): void
    {
        $this->get('/activities/create')->assertRedirect('/login');
    }

    public function test_admin_can_view_activity_create_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/activities/create')->assertOk();
    }

    public function test_admin_can_view_activity_edit_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $activity = Activity::create([
            'type' => 'call',
            'title' => 'Initial Call',
            'date' => '2026-08-10',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->get("/activities/{$activity->id}/edit")->assertOk();
    }

    public function test_admin_can_view_activity_show_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $activity = Activity::create([
            'type' => 'call',
            'title' => 'Initial Call',
            'date' => '2026-08-10',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->get("/activities/{$activity->id}")->assertOk();
    }

    public function test_support_role_cannot_access_activity_create(): void
    {
        $this->seedPermissions();

        $support = User::factory()->create(['role' => 'support']);

        $this->actingAs($support)->get('/activities/create')->assertForbidden();
    }

    public function test_admin_can_create_activity(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);

        Livewire::actingAs($admin)->test(ActivitiesCreate::class)
            ->set('type', 'meeting')
            ->set('title', 'Kickoff Meeting')
            ->set('description', 'Discuss project scope')
            ->set('date', '2026-08-10')
            ->set('time', '10:00')
            ->set('duration', 60)
            ->set('outcome', 'Meeting held')
            ->set('customer_id', $customer->id)
            ->set('assigned_to', $admin->id)
            ->call('save')
            ->assertRedirect(route('activities.index'));

        $this->assertDatabaseHas('activities', [
            'type' => 'meeting',
            'title' => 'Kickoff Meeting',
            'description' => 'Discuss project scope',
            'date' => '2026-08-10',
            'time' => '10:00',
            'duration' => 60,
            'outcome' => 'Meeting held',
            'customer_id' => $customer->id,
            'assigned_to' => $admin->id,
            'created_by' => $admin->id,
        ]);
    }

    public function test_create_activity_requires_type_title_and_date(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(ActivitiesCreate::class)
            ->set('title', '')
            ->set('date', null)
            ->call('save')
            ->assertHasErrors(['title', 'date']);
    }

    public function test_admin_can_edit_activity(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $activity = Activity::create([
            'type' => 'call',
            'title' => 'Initial Call',
            'date' => '2026-08-10',
            'created_by' => $admin->id,
        ]);

        Livewire::actingAs($admin)->test(ActivitiesEdit::class, ['activity' => $activity])
            ->set('type', 'email')
            ->set('title', 'Follow-up Email')
            ->set('date', '2026-08-12')
            ->set('duration', 30)
            ->call('save')
            ->assertRedirect(route('activities.index'));

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'type' => 'email',
            'title' => 'Follow-up Email',
            'date' => '2026-08-12',
            'duration' => 30,
            'created_by' => $admin->id,
        ]);
    }
}
