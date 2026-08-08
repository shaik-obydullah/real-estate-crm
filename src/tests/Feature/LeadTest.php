<?php

namespace Tests\Feature;

use App\Http\Livewire\Leads\Create as LeadsCreate;
use App\Http\Livewire\Leads\Edit as LeadsEdit;
use App\Models\Lead;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    protected function seedPermissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_from_lead_create(): void
    {
        $this->get('/leads/create')->assertRedirect('/login');
    }

    public function test_guest_is_redirected_from_lead_edit(): void
    {
        $lead = Lead::create([
            'title' => 'Guest Lead',
            'source' => 'website',
            'status' => 'new',
            'priority' => 'medium',
        ]);

        $this->get('/leads/'.$lead->id.'/edit')->assertRedirect('/login');
    }

    public function test_admin_can_view_lead_create_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/leads/create')->assertOk();
    }

    public function test_admin_can_view_lead_edit_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $lead = Lead::create([
            'title' => 'Existing Lead',
            'source' => 'referral',
            'status' => 'contacted',
            'priority' => 'high',
        ]);

        $this->actingAs($admin)->get('/leads/'.$lead->id.'/edit')->assertOk();
    }

    public function test_support_role_cannot_access_lead_create(): void
    {
        $this->seedPermissions();

        $support = User::factory()->create(['role' => 'support']);

        $this->actingAs($support)->get('/leads/create')->assertForbidden();
    }

    public function test_support_role_cannot_access_lead_edit(): void
    {
        $this->seedPermissions();

        $support = User::factory()->create(['role' => 'support']);
        $lead = Lead::create([
            'title' => 'Support Lead',
            'source' => 'website',
            'status' => 'new',
            'priority' => 'medium',
        ]);

        $this->actingAs($support)->get('/leads/'.$lead->id.'/edit')->assertForbidden();
    }

    public function test_admin_can_create_lead(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(LeadsCreate::class)
            ->set('title', 'Enterprise Software')
            ->set('company_name', 'Acme Corp')
            ->set('contact_name', 'Alice Liu')
            ->set('contact_email', 'alice@acme.com')
            ->set('contact_phone', '+1-555-1001')
            ->set('source', 'email_campaign')
            ->set('status', 'proposal_sent')
            ->set('priority', 'high')
            ->set('value', 45000)
            ->call('save')
            ->assertRedirect(route('leads.index'));

        $this->assertDatabaseHas('leads', [
            'title' => 'Enterprise Software',
            'company_name' => 'Acme Corp',
            'contact_name' => 'Alice Liu',
            'contact_email' => 'alice@acme.com',
            'contact_phone' => '+1-555-1001',
            'source' => 'email_campaign',
            'status' => 'proposal_sent',
            'priority' => 'high',
            'value' => 45000,
        ]);
    }

    public function test_create_lead_requires_title(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(LeadsCreate::class)
            ->set('title', '')
            ->call('save')
            ->assertHasErrors(['title']);
    }

    public function test_admin_can_edit_lead(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $lead = Lead::create([
            'title' => 'Original Lead',
            'company_name' => 'Globex LLC',
            'source' => 'referral',
            'status' => 'new',
            'priority' => 'medium',
            'value' => 10000,
        ]);

        Livewire::actingAs($admin)->test(LeadsEdit::class, ['lead' => $lead])
            ->set('title', 'Updated Lead')
            ->set('status', 'negotiation')
            ->set('priority', 'high')
            ->set('value', 20000)
            ->call('save')
            ->assertRedirect(route('leads.index'));

        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'title' => 'Updated Lead',
            'company_name' => 'Globex LLC',
            'status' => 'negotiation',
            'priority' => 'high',
            'value' => 20000,
        ]);
    }
}
