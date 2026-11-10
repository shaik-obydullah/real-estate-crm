<?php

namespace Tests\Feature;

use App\Http\Livewire\SalesOrders\Create as SalesOrdersCreate;
use App\Http\Livewire\SalesOrders\Edit as SalesOrdersEdit;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SalesOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function seedPermissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    protected function makeOrder(array $attributes = []): SalesOrder
    {
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);

        return SalesOrder::create(array_merge([
            'order_number' => 'SO-0001',
            'customer_id' => $customer->id,
            'subtotal' => 1000,
            'tax_amount' => 0,
            'discount' => 0,
            'total' => 1000,
            'status' => 'pending',
            'created_by' => User::factory()->create()->id,
        ], $attributes));
    }

    public function test_guest_is_redirected_from_sales_order_pages(): void
    {
        $this->get('/sales-orders/create')->assertRedirect('/login');

        $salesOrder = $this->makeOrder();

        $this->get('/sales-orders/' . $salesOrder->id)->assertRedirect('/login');
        $this->get('/sales-orders/' . $salesOrder->id . '/edit')->assertRedirect('/login');
    }

    public function test_admin_can_view_sales_order_create_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/sales-orders/create')->assertOk();
    }

    public function test_admin_can_view_sales_order_show_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $salesOrder = $this->makeOrder(['created_by' => $admin->id]);

        $this->actingAs($admin)->get('/sales-orders/' . $salesOrder->id)->assertOk();
    }

    public function test_admin_can_view_sales_order_edit_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $salesOrder = $this->makeOrder(['created_by' => $admin->id]);

        $this->actingAs($admin)->get('/sales-orders/' . $salesOrder->id . '/edit')->assertOk();
    }

    public function test_support_role_cannot_access_sales_order_create(): void
    {
        $this->seedPermissions();

        $support = User::factory()->create(['role' => 'support']);

        $this->actingAs($support)->get('/sales-orders/create')->assertForbidden();
    }

    public function test_admin_can_create_sales_order_with_line_items(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);
        $product = Product::create(['name' => 'Cloud Hosting', 'sku' => 'SKU-003', 'price' => 500]);

        Livewire::actingAs($admin)->test(SalesOrdersCreate::class)
            ->set('customer_id', $customer->id)
            ->set('status', 'confirmed')
            ->set('discount', 50)
            ->set('delivery_date', '2026-08-15')
            ->set('shipping_address', '123 Main St')
            ->set('items.0.product_id', $product->id)
            ->set('items.0.description', 'Cloud Hosting - 12 months')
            ->set('items.0.quantity', 12)
            ->set('items.0.unit_price', 500)
            ->set('items.1.description', 'Setup Fee')
            ->set('items.1.quantity', 1)
            ->set('items.1.unit_price', 250)
            ->call('save')
            ->assertRedirect(route('sales-orders.index'));

        $this->assertDatabaseHas('sales_orders', [
            'order_number' => 'SO-0001',
            'customer_id' => $customer->id,
            'subtotal' => 6250,
            'tax_amount' => 0,
            'discount' => 50,
            'total' => 6200,
            'status' => 'confirmed',
            'shipping_address' => '123 Main St',
            'created_by' => $admin->id,
        ]);

        $salesOrder = SalesOrder::where('order_number', 'SO-0001')->first();

        $this->assertEquals('2026-08-15', $salesOrder->delivery_date?->format('Y-m-d'));

        $this->assertSame(2, $salesOrder->items()->count());

        $this->assertDatabaseHas('sales_order_items', [
            'sales_order_id' => $salesOrder->id,
            'product_id' => $product->id,
            'description' => 'Cloud Hosting - 12 months',
            'quantity' => 12,
            'unit_price' => 500,
            'total' => 6000,
        ]);

        $this->assertDatabaseHas('sales_order_items', [
            'sales_order_id' => $salesOrder->id,
            'product_id' => null,
            'description' => 'Setup Fee',
            'quantity' => 1,
            'unit_price' => 250,
            'total' => 250,
        ]);
    }

    public function test_create_sales_order_requires_customer_and_item_description(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(SalesOrdersCreate::class)
            ->set('customer_id', null)
            ->set('items.0.quantity', 0)
            ->call('save')
            ->assertHasErrors(['customer_id', 'items.0.description', 'items.0.quantity']);
    }

    public function test_create_sales_order_generates_sequential_number(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Globex LLC', 'type' => 'company']);

        $component = Livewire::actingAs($admin)->test(SalesOrdersCreate::class);

        $component->assertSet('order_number', 'SO-0001');

        $component
            ->set('customer_id', $customer->id)
            ->set('items.0.description', 'Consulting')
            ->set('items.0.quantity', 1)
            ->set('items.0.unit_price', 100)
            ->call('save');

        $this->assertDatabaseHas('sales_orders', ['order_number' => 'SO-0001']);
    }

    public function test_admin_can_edit_sales_order(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);
        $otherCustomer = Customer::create(['name' => 'Globex LLC', 'type' => 'company']);

        $salesOrder = SalesOrder::create([
            'order_number' => 'SO-0001',
            'customer_id' => $customer->id,
            'subtotal' => 1000,
            'tax_amount' => 0,
            'discount' => 0,
            'total' => 1000,
            'status' => 'pending',
            'created_by' => $admin->id,
        ]);

        $salesOrder->items()->create([
            'description' => 'Old Item',
            'quantity' => 2,
            'unit_price' => 500,
            'total' => 1000,
        ]);

        Livewire::actingAs($admin)->test(SalesOrdersEdit::class, ['salesOrder' => $salesOrder])
            ->set('customer_id', $otherCustomer->id)
            ->set('status', 'confirmed')
            ->set('discount', 100)
            ->set('items.0.description', 'New Item')
            ->set('items.0.quantity', 10)
            ->set('items.0.unit_price', 200)
            ->call('save')
            ->assertRedirect(route('sales-orders.index'));

        $this->assertDatabaseHas('sales_orders', [
            'id' => $salesOrder->id,
            'order_number' => 'SO-0001',
            'customer_id' => $otherCustomer->id,
            'subtotal' => 2000,
            'tax_amount' => 0,
            'discount' => 100,
            'total' => 1900,
            'status' => 'confirmed',
        ]);

        $this->assertSame(1, $salesOrder->items()->count());

        $this->assertDatabaseHas('sales_order_items', [
            'sales_order_id' => $salesOrder->id,
            'description' => 'New Item',
            'quantity' => 10,
            'unit_price' => 200,
            'total' => 2000,
        ]);
    }
}
