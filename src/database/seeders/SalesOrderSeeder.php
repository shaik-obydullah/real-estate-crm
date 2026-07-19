<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Database\Seeder;

class SalesOrderSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@crm.com')->first();
        $jane = User::where('email', 'jane@crm.com')->first();

        $acme = Customer::where('name', 'Acme Corp')->first();
        $techlogix = Customer::where('name', 'TechLogix Inc')->first();

        $quo1 = Quotation::where('quote_number', 'QUO-0001')->first();
        $enterpriseLicense = Product::where('sku', 'SKU-001')->first();
        $profServices = Product::where('sku', 'SKU-002')->first();
        $cloudHosting = Product::where('sku', 'SKU-003')->first();
        $supportPlan = Product::where('sku', 'SKU-006')->first();

        $so1 = SalesOrder::create([
            'order_number' => 'SO-0001',
            'customer_id' => $acme->id,
            'quotation_id' => $quo1->id,
            'subtotal' => 41000,
            'tax_amount' => 4100,
            'discount' => 100,
            'total' => 45000,
            'status' => 'delivered',
            'delivery_date' => now()->subDays(5),
            'shipping_address' => '123 Main St, San Francisco, CA 94102',
            'notes' => 'Delivered to Acme Corp HQ',
            'created_by' => $admin->id,
        ]);

        $so1->items()->create([
            'product_id' => $enterpriseLicense->id,
            'description' => 'Enterprise License',
            'quantity' => 1,
            'unit_price' => 35000,
            'total' => 35000,
        ]);

        $so1->items()->create([
            'product_id' => $profServices->id,
            'description' => 'Professional Services - 40 hours',
            'quantity' => 40,
            'unit_price' => 150,
            'total' => 6000,
        ]);

        $so2 = SalesOrder::create([
            'order_number' => 'SO-0002',
            'customer_id' => $techlogix->id,
            'subtotal' => 109000,
            'tax_amount' => 11000,
            'discount' => 0,
            'total' => 120000,
            'status' => 'processing',
            'notes' => 'Cloud migration in progress',
            'created_by' => $jane->id,
        ]);

        $so2->items()->create([
            'product_id' => $cloudHosting->id,
            'description' => 'Cloud Hosting - 12 months',
            'quantity' => 12,
            'unit_price' => 500,
            'total' => 6000,
        ]);

        $so2->items()->create([
            'product_id' => $profServices->id,
            'description' => 'Migration Services - 200 hours',
            'quantity' => 200,
            'unit_price' => 150,
            'total' => 30000,
        ]);

        $so2->items()->create([
            'product_id' => $supportPlan->id,
            'description' => 'Support Plan - 12 months',
            'quantity' => 12,
            'unit_price' => 1000,
            'total' => 12000,
        ]);
    }
}
