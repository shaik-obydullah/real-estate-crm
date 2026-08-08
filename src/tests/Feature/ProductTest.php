<?php

namespace Tests\Feature;

use App\Http\Livewire\Products\Create as ProductsCreate;
use App\Http\Livewire\Products\Edit as ProductsEdit;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function seedPermissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_from_product_pages(): void
    {
        $this->get('/products/create')->assertRedirect('/login');

        $product = Product::create([
            'name' => 'Skyline Villa',
            'sku' => 'PROD-001',
            'price' => 1499.99,
        ]);

        $this->get('/products/' . $product->id)->assertRedirect('/login');
        $this->get('/products/' . $product->id . '/edit')->assertRedirect('/login');
    }

    public function test_admin_can_view_product_create_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/products/create')->assertOk();
    }

    public function test_admin_can_view_product_show_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::create([
            'name' => 'Skyline Villa',
            'sku' => 'PROD-001',
            'price' => 1499.99,
        ]);

        $this->actingAs($admin)->get('/products/' . $product->id)->assertOk();
    }

    public function test_admin_can_view_product_edit_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::create([
            'name' => 'Skyline Villa',
            'sku' => 'PROD-001',
            'price' => 1499.99,
        ]);

        $this->actingAs($admin)->get('/products/' . $product->id . '/edit')->assertOk();
    }

    public function test_support_role_cannot_access_product_create(): void
    {
        $this->seedPermissions();

        $support = User::factory()->create(['role' => 'support']);

        $this->actingAs($support)->get('/products/create')->assertForbidden();
    }

    public function test_admin_can_create_product(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(ProductsCreate::class)
            ->set('name', 'Skyline Villa')
            ->set('sku', 'PROD-001')
            ->set('description', 'A luxury villa with a view.')
            ->set('price', 1499.99)
            ->set('cost', 1200.00)
            ->set('category', 'villa')
            ->set('stock', 25)
            ->set('status', 'active')
            ->call('save')
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Skyline Villa',
            'sku' => 'PROD-001',
            'description' => 'A luxury villa with a view.',
            'price' => 1499.99,
            'cost' => 1200.00,
            'category' => 'villa',
            'stock' => 25,
            'status' => 'active',
        ]);
    }

    public function test_create_product_requires_name_sku_and_price(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(ProductsCreate::class)
            ->set('name', '')
            ->set('sku', '')
            ->set('price', null)
            ->call('save')
            ->assertHasErrors(['name', 'sku', 'price']);
    }

    public function test_create_product_validates_unique_sku(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        Product::create([
            'name' => 'Existing Villa',
            'sku' => 'PROD-001',
            'price' => 999.00,
        ]);

        Livewire::actingAs($admin)->test(ProductsCreate::class)
            ->set('name', 'New Villa')
            ->set('sku', 'PROD-001')
            ->set('price', 1200.00)
            ->call('save')
            ->assertHasErrors(['sku']);
    }

    public function test_admin_can_edit_product(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::create([
            'name' => 'Skyline Villa',
            'sku' => 'PROD-001',
            'price' => 1499.99,
            'stock' => 10,
            'status' => 'active',
        ]);

        Livewire::actingAs($admin)->test(ProductsEdit::class, ['product' => $product])
            ->set('name', 'Oceanview Villa')
            ->set('sku', 'PROD-002')
            ->set('price', 1999.99)
            ->set('stock', 5)
            ->set('status', 'inactive')
            ->call('save')
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Oceanview Villa',
            'sku' => 'PROD-002',
            'price' => 1999.99,
            'stock' => 5,
            'status' => 'inactive',
        ]);
    }

    public function test_edit_product_allows_own_sku(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::create([
            'name' => 'Skyline Villa',
            'sku' => 'PROD-001',
            'price' => 1499.99,
        ]);

        Livewire::actingAs($admin)->test(ProductsEdit::class, ['product' => $product])
            ->set('name', 'Skyline Villa')
            ->set('sku', 'PROD-001')
            ->set('price', 1499.99)
            ->call('save')
            ->assertRedirect(route('products.index'));
    }

    public function test_edit_product_rejects_another_products_sku(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        Product::create([
            'name' => 'Existing Villa',
            'sku' => 'PROD-001',
            'price' => 999.00,
        ]);
        $product = Product::create([
            'name' => 'Skyline Villa',
            'sku' => 'PROD-002',
            'price' => 1499.99,
        ]);

        Livewire::actingAs($admin)->test(ProductsEdit::class, ['product' => $product])
            ->set('name', 'Skyline Villa')
            ->set('sku', 'PROD-001')
            ->set('price', 1499.99)
            ->call('save')
            ->assertHasErrors(['sku']);
    }
}
