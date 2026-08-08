<?php

namespace Tests\Feature;

use App\Http\Livewire\Invoices\Create as InvoicesCreate;
use App\Http\Livewire\Invoices\Edit as InvoicesEdit;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function seedPermissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    protected function createInvoice(User $user, Customer $customer, array $overrides = []): Invoice
    {
        return Invoice::create(array_merge([
            'invoice_number' => 'INV-0001',
            'customer_id' => $customer->id,
            'subtotal' => 1000,
            'tax_amount' => 100,
            'discount' => 0,
            'total' => 1100,
            'paid_amount' => 0,
            'status' => 'draft',
            'due_date' => now()->addDays(30),
            'created_by' => $user->id,
        ], $overrides));
    }

    public function test_guest_is_redirected_from_invoice_create(): void
    {
        $this->get('/invoices/create')->assertRedirect('/login');
    }

    public function test_admin_can_view_invoice_create_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/invoices/create')->assertOk();
    }

    public function test_support_role_cannot_access_invoice_create(): void
    {
        $this->seedPermissions();

        $support = User::factory()->create(['role' => 'support']);

        $this->actingAs($support)->get('/invoices/create')->assertForbidden();
    }

    public function test_admin_can_view_invoice_show_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);
        $invoice = $this->createInvoice($admin, $customer);

        $this->actingAs($admin)->get('/invoices/'.$invoice->id)->assertOk();
    }

    public function test_admin_can_view_invoice_edit_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);
        $invoice = $this->createInvoice($admin, $customer);

        $this->actingAs($admin)->get('/invoices/'.$invoice->id.'/edit')->assertOk();
    }

    public function test_admin_can_create_invoice_with_line_items(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);

        Livewire::actingAs($admin)->test(InvoicesCreate::class)
            ->set('customer_id', $customer->id)
            ->set('due_date', '2026-09-01')
            ->set('discount', 10)
            ->set('items', [
                ['product_id' => null, 'description' => 'Consulting', 'quantity' => 10, 'unit_price' => 100, 'tax_rate' => 10],
                ['product_id' => null, 'description' => 'Hardware Kit', 'quantity' => 2, 'unit_price' => 50, 'tax_rate' => 0],
            ])
            ->call('save')
            ->assertRedirect(route('invoices.index'));

        $this->assertDatabaseHas('invoices', [
            'invoice_number' => 'INV-0001',
            'customer_id' => $customer->id,
            'subtotal' => 1100,
            'tax_amount' => 100,
            'discount' => 10,
            'total' => 1190,
            'status' => 'draft',
            'due_date' => '2026-09-01 00:00:00',
            'created_by' => $admin->id,
        ]);

        $this->assertSame(2, InvoiceItem::count());

        $this->assertDatabaseHas('invoice_items', [
            'description' => 'Consulting',
            'quantity' => 10,
            'unit_price' => 100,
            'tax_rate' => 10,
            'total' => 1100,
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'description' => 'Hardware Kit',
            'quantity' => 2,
            'unit_price' => 50,
            'tax_rate' => 0,
            'total' => 100,
        ]);
    }

    public function test_create_invoice_requires_customer_due_date_and_item_description(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(InvoicesCreate::class)
            ->set('customer_id', null)
            ->set('due_date', '')
            ->set('items', [
                ['product_id' => null, 'description' => '', 'quantity' => 0, 'unit_price' => 0, 'tax_rate' => 0],
            ])
            ->call('save')
            ->assertHasErrors(['customer_id', 'due_date', 'items.0.description', 'items.0.quantity']);
    }

    public function test_create_invoice_auto_generates_invoice_number(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(InvoicesCreate::class)
            ->assertSet('invoice_number', 'INV-0001');
    }

    public function test_admin_can_edit_invoice(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);
        $other = Customer::create(['name' => 'TechLogix Inc', 'type' => 'company']);
        $invoice = $this->createInvoice($admin, $customer);

        $invoice->items()->create([
            'description' => 'Old Item',
            'quantity' => 1,
            'unit_price' => 100,
            'tax_rate' => 0,
            'total' => 100,
        ]);

        Livewire::actingAs($admin)->test(InvoicesEdit::class, ['invoice' => $invoice])
            ->set('customer_id', $other->id)
            ->set('due_date', '2026-12-31')
            ->set('status', 'sent')
            ->set('discount', 50)
            ->set('items', [
                ['product_id' => null, 'description' => 'Updated Item', 'quantity' => 3, 'unit_price' => 200, 'tax_rate' => 10],
            ])
            ->call('save')
            ->assertRedirect(route('invoices.index'));

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'invoice_number' => 'INV-0001',
            'customer_id' => $other->id,
            'subtotal' => 600,
            'tax_amount' => 60,
            'discount' => 50,
            'total' => 610,
            'status' => 'sent',
            'due_date' => '2026-12-31 00:00:00',
        ]);

        $this->assertSame(1, InvoiceItem::count());

        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'description' => 'Updated Item',
            'quantity' => 3,
            'unit_price' => 200,
            'tax_rate' => 10,
            'total' => 660,
        ]);
    }

    public function test_edit_invoice_keeps_items_when_all_rows_blank(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);
        $invoice = $this->createInvoice($admin, $customer);

        $invoice->items()->create([
            'description' => 'Keep Me',
            'quantity' => 1,
            'unit_price' => 100,
            'tax_rate' => 0,
            'total' => 100,
        ]);

        Livewire::actingAs($admin)->test(InvoicesEdit::class, ['invoice' => $invoice])
            ->set('due_date', '2026-10-01')
            ->set('items', [])
            ->call('save')
            ->assertRedirect(route('invoices.index'));

        $this->assertSame(1, InvoiceItem::count());
        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'description' => 'Keep Me',
        ]);
    }
}
