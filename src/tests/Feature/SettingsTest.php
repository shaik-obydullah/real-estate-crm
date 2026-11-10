<?php

namespace Tests\Feature;

use App\Http\Livewire\Settings\Index as SettingsIndex;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function seedPermissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_settings_page_requires_settings_permission(): void
    {
        $this->seedPermissions();

        $sales = User::factory()->create(['role' => 'sales']);

        $this->actingAs($sales)->get('/settings')->assertForbidden();
    }

    public function test_admin_can_view_settings_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/settings')->assertOk();
    }

    public function test_admin_can_save_general_and_maintenance_settings(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(SettingsIndex::class)
            ->set('companyName', 'Acme Realty')
            ->set('maintenanceEnabled', true)
            ->set('maintenanceMessage', 'Scheduled maintenance tonight')
            ->set('maintenanceAllowedIps', '203.0.113.10')
            ->call('saveGeneral')
            ->assertHasNoErrors();

        $this->assertSame('Acme Realty', Setting::where('key', 'company_name')->value('value'));
        $this->assertSame('1', Setting::where('key', 'maintenance_enabled')->value('value'));
        $this->assertSame('Scheduled maintenance tonight', Setting::where('key', 'maintenance_message')->value('value'));
        $this->assertSame('203.0.113.10', Setting::where('key', 'maintenance_allowed_ips')->value('value'));
    }

    public function test_admin_can_save_localization_settings(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(SettingsIndex::class)
            ->set('timezone', 'Asia/Dubai')
            ->set('dateFormat', 'd/m/Y')
            ->set('timeFormat', 'H:i')
            ->call('saveLocalization')
            ->assertHasNoErrors();

        $this->assertSame('Asia/Dubai', Setting::where('key', 'timezone')->value('value'));
        $this->assertSame('d/m/Y', Setting::where('key', 'date_format')->value('value'));
        $this->assertSame('H:i', Setting::where('key', 'time_format')->value('value'));
    }

    public function test_maintenance_mode_blocks_non_admin_users(): void
    {
        $this->seedPermissions();

        Setting::create(['key' => 'maintenance_enabled', 'value' => '1']);

        $sales = User::factory()->create(['role' => 'sales']);

        $this->actingAs($sales)->get('/dashboard')->assertStatus(503);
    }

    public function test_maintenance_mode_allows_admins(): void
    {
        $this->seedPermissions();

        Setting::create(['key' => 'maintenance_enabled', 'value' => '1']);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/dashboard')->assertOk();
    }

    public function test_maintenance_mode_allows_whitelisted_ips(): void
    {
        $this->seedPermissions();

        Setting::create(['key' => 'maintenance_enabled', 'value' => '1']);
        Setting::create(['key' => 'maintenance_allowed_ips', 'value' => '127.0.0.1']);

        $sales = User::factory()->create(['role' => 'sales']);

        $this->actingAs($sales)->get('/dashboard')->assertOk();
    }

    public function test_maintenance_mode_blocks_guests(): void
    {
        $this->seedPermissions();

        Setting::create(['key' => 'maintenance_enabled', 'value' => '1']);

        $this->get('/dashboard')->assertStatus(503);
    }

    public function test_login_page_is_available_during_maintenance(): void
    {
        $this->seedPermissions();

        Setting::create(['key' => 'maintenance_enabled', 'value' => '1']);

        $this->get('/login')->assertOk();
    }

    public function test_configured_timezone_is_applied(): void
    {
        $this->seedPermissions();

        Setting::create(['key' => 'timezone', 'value' => 'America/New_York']);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/dashboard');

        $this->assertSame('America/New_York', config('app.timezone'));
        $this->assertSame('America/New_York', date_default_timezone_get());
    }

    public function test_app_settings_helpers_format_dates(): void
    {
        Setting::create(['key' => 'date_format', 'value' => 'd/m/Y']);
        Setting::create(['key' => 'time_format', 'value' => 'H:i']);

        $this->assertSame('08/08/2026', format_date('2026-08-08 15:05:00'));
        $this->assertSame('15:05', format_time('2026-08-08 15:05:00'));
        $this->assertSame('08/08/2026 15:05', format_datetime('2026-08-08 15:05:00'));
        $this->assertSame('', format_date(null));
    }
}
