<?php

namespace Tests\Feature;

use App\Http\Livewire\Reports\Index as ReportsIndex;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    protected function seedPermissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_view_reports_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/reports')->assertOk();
    }

    public function test_csv_download(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(ReportsIndex::class)
            ->call('exportData', 'csv')
            ->assertFileDownloaded('crm-report-' . now()->format('Y-m-d') . '.csv');
    }

    public function test_excel_download(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(ReportsIndex::class)
            ->call('exportData', 'excel')
            ->assertFileDownloaded('crm-report-' . now()->format('Y-m-d') . '.xls');
    }

    public function test_pdf_download(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(ReportsIndex::class)
            ->call('exportData', 'pdf')
            ->assertFileDownloaded('crm-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
