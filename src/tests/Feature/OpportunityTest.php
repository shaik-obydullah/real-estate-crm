<?php

namespace Tests\Feature;

use App\Http\Livewire\Opportunities\Create as OpportunitiesCreate;
use App\Http\Livewire\Opportunities\Edit as OpportunitiesEdit;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OpportunityTest extends TestCase
{
    use RefreshDatabase;

    protected function seedPermissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_from_opportunity_create(): void
    {
        $this->get('/opportunities/create')->assertRedirect('/login');
    }

    public function test_guest_is_redirected_from_opportunity_edit(): void
    {
        $opportunity = Opportunity::create([
            'name' => 'Guest Opportunity',
        ]);

        $this->get('/opportunities/'.$opportunity->id.'/edit')->assertRedirect('/login');
    }

    public function test_admin_can_view_opportunity_create_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/opportunities/create')->assertOk();
    }

    public function test_admin_can_view_opportunity_edit_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $opportunity = Opportunity::create([
            'name' => 'Existing Opportunity',
        ]);

        $this->actingAs($admin)->get('/opportunities/'.$opportunity->id.'/edit')->assertOk();
    }

    public function test_support_role_cannot_access_opportunity_create(): void
    {
        $this->seedPermissions();

        $support = User::factory()->create(['role' => 'support']);

        $this->actingAs($support)->get('/opportunities/create')->assertForbidden();
    }

    public function test_support_role_cannot_access_opportunity_edit(): void
    {
        $this->seedPermissions();

        $support = User::factory()->create(['role' => 'support']);
        $opportunity = Opportunity::create([
            'name' => 'Support Opportunity',
        ]);

        $this->actingAs($support)->get('/opportunities/'.$opportunity->id.'/edit')->assertForbidden();
    }

    public function test_admin_can_create_opportunity(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);
        $contact = Contact::create([
            'customer_id' => $customer->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);
        $lead = Lead::create([
            'title' => 'Related Lead',
            'source' => 'website',
            'status' => 'new',
            'priority' => 'medium',
        ]);

        Livewire::actingAs($admin)->test(OpportunitiesCreate::class)
            ->set('name', 'Cloud Migration')
            ->set('company_name', 'Acme Corp')
            ->set('contact_id', $contact->id)
            ->set('lead_id', $lead->id)
            ->set('value', 120000)
            ->set('stage', 'proposal')
            ->set('expected_closing_date', '2026-12-31')
            ->call('save')
            ->assertRedirect(route('opportunities.index'));

        $this->assertDatabaseHas('opportunities', [
            'name' => 'Cloud Migration',
            'company_name' => 'Acme Corp',
            'contact_id' => $contact->id,
            'lead_id' => $lead->id,
            'value' => 120000,
            'stage' => 'proposal',
            'probability' => 60,
        ]);
    }

    public function test_stage_change_auto_sets_probability(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(OpportunitiesCreate::class)
            ->set('name', 'Hardware Upgrade')
            ->set('stage', 'won')
            ->call('save')
            ->assertRedirect(route('opportunities.index'));

        $this->assertDatabaseHas('opportunities', [
            'name' => 'Hardware Upgrade',
            'stage' => 'won',
            'probability' => 100,
        ]);
    }

    public function test_create_opportunity_requires_name(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(OpportunitiesCreate::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    public function test_admin_can_edit_opportunity(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $opportunity = Opportunity::create([
            'name' => 'Original Opportunity',
            'company_name' => 'Globex LLC',
            'value' => 10000,
            'stage' => 'new',
            'probability' => 10,
        ]);

        Livewire::actingAs($admin)->test(OpportunitiesEdit::class, ['opportunity' => $opportunity])
            ->set('name', 'Updated Opportunity')
            ->set('company_name', 'TechLogix Inc')
            ->set('value', 25000)
            ->set('stage', 'negotiation')
            ->call('save')
            ->assertRedirect(route('opportunities.index'));

        $this->assertDatabaseHas('opportunities', [
            'id' => $opportunity->id,
            'name' => 'Updated Opportunity',
            'company_name' => 'TechLogix Inc',
            'value' => 25000,
            'stage' => 'negotiation',
            'probability' => 75,
        ]);
    }
}
