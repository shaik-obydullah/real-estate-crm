<?php

namespace Tests\Feature;

use App\Http\Livewire\Quotations\Create as QuotationsCreate;
use App\Http\Livewire\Quotations\Edit as QuotationsEdit;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuotationTest extends TestCase
{
    use RefreshDatabase;

    protected function seedPermissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_from_quotation_create(): void
    {
        $this->get('/quotations/create')->assertRedirect('/login');
    }

    public function test_admin_can_view_quotation_create_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/quotations/create')->assertOk();
    }

    public function test_admin_can_view_quotation_show_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);
        $quotation = Quotation::create([
            'quote_number' => 'QUO-0001',
            'customer_id' => $customer->id,
            'subtotal' => 100,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'discount' => 0,
            'total' => 100,
            'status' => 'draft',
            'valid_until' => now()->addDays(30),
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->get(route('quotations.show', $quotation))->assertOk();
    }

    public function test_admin_can_view_quotation_edit_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);
        $quotation = Quotation::create([
            'quote_number' => 'QUO-0001',
            'customer_id' => $customer->id,
            'subtotal' => 100,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'discount' => 0,
            'total' => 100,
            'status' => 'draft',
            'valid_until' => now()->addDays(30),
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->get(route('quotations.edit', $quotation))->assertOk();
    }

    public function test_support_role_cannot_access_quotation_create(): void
    {
        $this->seedPermissions();

        $support = User::factory()->create(['role' => 'support']);

        $this->actingAs($support)->get('/quotations/create')->assertForbidden();
    }

    public function test_admin_can_create_quotation_with_line_items(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);
        $product = Product::create(['name' => 'Enterprise License', 'sku' => 'SKU-001', 'price' => 100, 'status' => 'active']);

        Livewire::actingAs($admin)->test(QuotationsCreate::class)
            ->set('customer_id', $customer->id)
            ->set('valid_until', now()->addDays(30)->format('Y-m-d'))
            ->set('items', [
                ['product_id' => $product->id, 'description' => 'Enterprise License', 'quantity' => 2, 'unit_price' => 100, 'tax_rate' => 10, 'discount' => 0],
                ['product_id' => null, 'description' => 'Setup Fee', 'quantity' => 1, 'unit_price' => 50, 'tax_rate' => 0, 'discount' => 0],
            ])
            ->call('save')
            ->assertRedirect(route('quotations.index'));

        $quotation = Quotation::where('quote_number', 'QUO-0001')->first();
        $this->assertNotNull($quotation);
        $this->assertSame($customer->id, $quotation->customer_id);
        $this->assertSame($admin->id, $quotation->created_by);
        $this->assertEquals(250, (float) $quotation->subtotal);
        $this->assertEquals(20, (float) $quotation->tax_amount);
        $this->assertEquals(270, (float) $quotation->total);
        $this->assertSame(2, $quotation->items()->count());

        $first = $quotation->items()->where('description', 'Enterprise License')->first();
        $this->assertNotNull($first);
        $this->assertEquals(220, (float) $first->total);

        $second = $quotation->items()->where('description', 'Setup Fee')->first();
        $this->assertNotNull($second);
        $this->assertEquals(50, (float) $second->total);
    }

    public function test_create_quotation_requires_customer_valid_until_and_items(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(QuotationsCreate::class)
            ->set('customer_id', null)
            ->set('valid_until', null)
            ->set('items', [
                ['product_id' => null, 'description' => '', 'quantity' => 1, 'unit_price' => 0, 'tax_rate' => 0, 'discount' => 0],
            ])
            ->call('save')
            ->assertHasErrors(['customer_id', 'valid_until', 'items.0.description']);
    }

    public function test_edit_updates_header_and_items(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);
        $otherCustomer = Customer::create(['name' => 'Globex LLC', 'type' => 'company']);

        $quotation = Quotation::create([
            'quote_number' => 'QUO-0001',
            'customer_id' => $customer->id,
            'subtotal' => 100,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'discount' => 0,
            'total' => 100,
            'status' => 'draft',
            'valid_until' => now()->addDays(30),
            'created_by' => $admin->id,
        ]);
        $quotation->items()->create([
            'description' => 'Old Item',
            'quantity' => 1,
            'unit_price' => 100,
            'tax_rate' => 0,
            'discount' => 0,
            'total' => 100,
        ]);

        Livewire::actingAs($admin)->test(QuotationsEdit::class, ['quotation' => $quotation])
            ->set('customer_id', $otherCustomer->id)
            ->set('valid_until', now()->addDays(60)->format('Y-m-d'))
            ->set('status', 'sent')
            ->set('items', [
                ['product_id' => null, 'description' => 'New Item A', 'quantity' => 2, 'unit_price' => 50, 'tax_rate' => 0, 'discount' => 0],
                ['product_id' => null, 'description' => 'New Item B', 'quantity' => 1, 'unit_price' => 100, 'tax_rate' => 10, 'discount' => 10],
            ])
            ->call('save')
            ->assertRedirect(route('quotations.index'));

        $quotation->refresh();
        $this->assertSame($otherCustomer->id, $quotation->customer_id);
        $this->assertSame('sent', $quotation->status);
        $this->assertEquals(190, (float) $quotation->subtotal);
        $this->assertEquals(10, (float) $quotation->tax_amount);
        $this->assertEquals(200, (float) $quotation->total);
        $this->assertSame(2, $quotation->items()->count());
        $this->assertDatabaseHas('quotation_items', ['description' => 'New Item A']);
        $this->assertDatabaseMissing('quotation_items', ['description' => 'Old Item']);

        $itemB = $quotation->items()->where('description', 'New Item B')->first();
        $this->assertEquals(100, (float) $itemB->total);
    }
}
