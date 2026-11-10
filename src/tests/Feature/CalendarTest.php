<?php

namespace Tests\Feature;

use App\Http\Livewire\Calendar\Create as CalendarCreate;
use App\Http\Livewire\Calendar\Edit as CalendarEdit;
use App\Models\CalendarEvent;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    protected function seedPermissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_from_calendar_create(): void
    {
        $this->get('/calendar/create')->assertRedirect('/login');
    }

    public function test_admin_can_view_calendar_create_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/calendar/create')->assertOk();
    }

    public function test_admin_can_view_calendar_edit_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $event = CalendarEvent::create([
            'title' => 'Client Meeting',
            'start_time' => '2026-08-10 09:00:00',
            'end_time' => '2026-08-10 10:00:00',
            'type' => 'meeting',
            'user_id' => $admin->id,
        ]);

        $this->actingAs($admin)->get("/calendar/{$event->id}/edit")->assertOk();
    }

    public function test_admin_can_view_calendar_show_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $event = CalendarEvent::create([
            'title' => 'Client Meeting',
            'start_time' => '2026-08-10 09:00:00',
            'end_time' => '2026-08-10 10:00:00',
            'type' => 'meeting',
            'user_id' => $admin->id,
        ]);

        $this->actingAs($admin)->get("/calendar/{$event->id}")->assertOk();
    }

    public function test_support_role_cannot_access_calendar_create(): void
    {
        $this->seedPermissions();

        $support = User::factory()->create(['role' => 'support']);

        $this->actingAs($support)->get('/calendar/create')->assertForbidden();
    }

    public function test_admin_can_create_calendar_event(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);

        Livewire::actingAs($admin)->test(CalendarCreate::class)
            ->set('title', 'Product Demo')
            ->set('description', 'Demo of the new features')
            ->set('start_time', '2026-08-10T09:00')
            ->set('end_time', '2026-08-10T10:30')
            ->set('location', 'Conference Room A')
            ->set('type', 'meeting')
            ->set('user_id', $admin->id)
            ->set('customer_id', $customer->id)
            ->call('save')
            ->assertRedirect(route('calendar.index'));

        $this->assertDatabaseHas('calendar_events', [
            'title' => 'Product Demo',
            'description' => 'Demo of the new features',
            'start_time' => '2026-08-10 09:00:00',
            'end_time' => '2026-08-10 10:30:00',
            'location' => 'Conference Room A',
            'type' => 'meeting',
            'user_id' => $admin->id,
            'customer_id' => $customer->id,
        ]);
    }

    public function test_create_calendar_event_requires_title_and_times(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(CalendarCreate::class)
            ->set('title', '')
            ->set('start_time', null)
            ->set('end_time', null)
            ->call('save')
            ->assertHasErrors(['title', 'start_time', 'end_time']);
    }

    public function test_create_calendar_event_requires_end_time_after_start_time(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(CalendarCreate::class)
            ->set('title', 'Backwards Event')
            ->set('start_time', '2026-08-10T10:00')
            ->set('end_time', '2026-08-10T09:00')
            ->call('save')
            ->assertHasErrors(['end_time']);
    }

    public function test_admin_can_edit_calendar_event(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $event = CalendarEvent::create([
            'title' => 'Client Meeting',
            'start_time' => '2026-08-10 09:00:00',
            'end_time' => '2026-08-10 10:00:00',
            'type' => 'meeting',
            'user_id' => $admin->id,
        ]);

        Livewire::actingAs($admin)->test(CalendarEdit::class, ['event' => $event])
            ->set('title', 'Rescheduled Meeting')
            ->set('start_time', '2026-08-12T14:00')
            ->set('end_time', '2026-08-12T15:00')
            ->set('type', 'call')
            ->call('save')
            ->assertRedirect(route('calendar.index'));

        $this->assertDatabaseHas('calendar_events', [
            'id' => $event->id,
            'title' => 'Rescheduled Meeting',
            'start_time' => '2026-08-12 14:00:00',
            'end_time' => '2026-08-12 15:00:00',
            'type' => 'call',
            'user_id' => $admin->id,
        ]);
    }
}
