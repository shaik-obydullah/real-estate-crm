<?php

namespace Tests\Feature;

use App\Http\Livewire\Contacts\Create as ContactsCreate;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    protected function seedPermissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_from_contact_create(): void
    {
        $this->get('/contacts/create')->assertRedirect('/login');
    }

    public function test_admin_can_view_contact_create_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/contacts/create')->assertOk();
    }

    public function test_support_role_cannot_access_contact_create(): void
    {
        $this->seedPermissions();

        $support = User::factory()->create(['role' => 'support']);

        $this->actingAs($support)->get('/contacts/create')->assertForbidden();
    }

    public function test_admin_can_create_contact(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);

        Livewire::actingAs($admin)->test(ContactsCreate::class)
            ->set('customer_id', $customer->id)
            ->set('first_name', 'Jane')
            ->set('last_name', 'Doe')
            ->set('email', 'jane@acme.com')
            ->set('phone', '+1-555-2001')
            ->set('position', 'CFO')
            ->set('department', 'Finance')
            ->call('save')
            ->assertRedirect(route('contacts.index'));

        $this->assertDatabaseHas('contacts', [
            'customer_id' => $customer->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@acme.com',
            'phone' => '+1-555-2001',
            'position' => 'CFO',
            'department' => 'Finance',
        ]);
    }

    public function test_create_contact_requires_company_and_name(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(ContactsCreate::class)
            ->set('customer_id', null)
            ->set('first_name', '')
            ->set('last_name', '')
            ->call('save')
            ->assertHasErrors(['customer_id', 'first_name', 'last_name']);
    }

    public function test_first_contact_for_customer_auto_becomes_primary(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Globex LLC', 'type' => 'company']);

        Livewire::actingAs($admin)->test(ContactsCreate::class)
            ->set('customer_id', $customer->id)
            ->set('first_name', 'John')
            ->set('last_name', 'Smith')
            ->set('is_primary', false)
            ->call('save');

        $this->assertSame(
            1,
            Contact::where('customer_id', $customer->id)->where('is_primary', true)->count()
        );
    }

    public function test_creating_primary_contact_unsets_previous_primary(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'TechLogix Inc', 'type' => 'company']);
        $first = Contact::create([
            'customer_id' => $customer->id,
            'first_name' => 'First',
            'last_name' => 'Contact',
            'is_primary' => true,
        ]);

        Livewire::actingAs($admin)->test(ContactsCreate::class)
            ->set('customer_id', $customer->id)
            ->set('first_name', 'Second')
            ->set('last_name', 'Contact')
            ->set('is_primary', true)
            ->call('save');

        $this->assertFalse($first->fresh()->is_primary);

        $newPrimary = Contact::where('customer_id', $customer->id)
            ->where('first_name', 'Second')
            ->first();

        $this->assertTrue($newPrimary->is_primary);
    }
}
