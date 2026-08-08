<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $jane = User::where('email', 'jane@crm.com')->first();
        $john = User::where('email', 'john@crm.com')->first();
        $mike = User::where('email', 'mike@crm.com')->first();

        $acme = Customer::where('name', 'Acme Corp')->first();
        $techlogix = Customer::where('name', 'TechLogix Inc')->first();
        $globex = Customer::where('name', 'Globex LLC')->first();

        $so1 = SalesOrder::where('order_number', 'SO-0001')->first();
        $enterpriseLicense = Product::where('sku', 'SKU-001')->first();
        $profServices = Product::where('sku', 'SKU-002')->first();
        $cloudHosting = Product::where('sku', 'SKU-003')->first();
        $hardwareKit = Product::where('sku', 'SKU-005')->first();
        $supportPlan = Product::where('sku', 'SKU-006')->first();

        $inv1 = Invoice::create([
            'invoice_number' => 'INV-0042',
            'customer_id' => $acme->id,
            'sales_order_id' => $so1->id,
            'subtotal' => 41000,
            'tax_amount' => 4100,
            'discount' => 100,
            'total' => 43100,
            'paid_amount' => 43100,
            'status' => 'paid',
            'due_date' => now()->subDays(19),
            'paid_date' => now()->subDays(22),
            'created_by' => $jane->id,
        ]);

        $inv1->items()->create([
            'product_id' => $enterpriseLicense->id,
            'description' => 'Enterprise License',
            'quantity' => 1,
            'unit_price' => 35000,
            'tax_rate' => 10,
            'total' => 35000,
        ]);

        $inv1->items()->create([
            'product_id' => $profServices->id,
            'description' => 'Professional Services - 40 hours',
            'quantity' => 40,
            'unit_price' => 150,
            'tax_rate' => 10,
            'total' => 6000,
        ]);

        $inv2 = Invoice::create([
            'invoice_number' => 'INV-0043',
            'customer_id' => $techlogix->id,
            'subtotal' => 109000,
            'tax_amount' => 11000,
            'discount' => 0,
            'total' => 120000,
            'paid_amount' => 0,
            'status' => 'sent',
            'due_date' => now()->addDays(27),
            'created_by' => $john->id,
        ]);

        $inv2->items()->create([
            'product_id' => $cloudHosting->id,
            'description' => 'Cloud Hosting - 12 months',
            'quantity' => 12,
            'unit_price' => 500,
            'tax_rate' => 10,
            'total' => 6000,
        ]);

        $inv2->items()->create([
            'product_id' => $profServices->id,
            'description' => 'Migration Services',
            'quantity' => 200,
            'unit_price' => 150,
            'tax_rate' => 10,
            'total' => 30000,
        ]);

        $inv3 = Invoice::create([
            'invoice_number' => 'INV-0044',
            'customer_id' => $globex->id,
            'subtotal' => 25500,
            'tax_amount' => 2500,
            'discount' => 0,
            'total' => 28000,
            'paid_amount' => 14000,
            'status' => 'partial',
            'due_date' => now()->addDays(43),
            'created_by' => $jane->id,
        ]);

        $inv3->items()->create([
            'product_id' => $profServices->id,
            'description' => 'Security Audit - 80 hours',
            'quantity' => 80,
            'unit_price' => 150,
            'tax_rate' => 10,
            'total' => 12000,
        ]);

        $inv3->items()->create([
            'product_id' => $supportPlan->id,
            'description' => 'Post-audit Support - 3 months',
            'quantity' => 3,
            'unit_price' => 1000,
            'tax_rate' => 10,
            'total' => 3000,
        ]);

        $inv4 = Invoice::create([
            'invoice_number' => 'INV-0045',
            'customer_id' => $acme->id,
            'subtotal' => 15000,
            'tax_amount' => 0,
            'discount' => 0,
            'total' => 15000,
            'paid_amount' => 15000,
            'status' => 'paid',
            'due_date' => now()->subDays(5),
            'paid_date' => now()->subDays(10),
            'created_by' => $mike->id,
        ]);

        $inv4->items()->create([
            'product_id' => $hardwareKit->id,
            'description' => 'Hardware Kit',
            'quantity' => 6,
            'unit_price' => 2500,
            'tax_rate' => 0,
            'total' => 15000,
        ]);

        $inv5 = Invoice::create([
            'invoice_number' => 'INV-0046',
            'customer_id' => $globex->id,
            'subtotal' => 60000,
            'tax_amount' => 0,
            'discount' => 0,
            'total' => 60000,
            'paid_amount' => 0,
            'status' => 'overdue',
            'due_date' => now()->subDays(19),
            'created_by' => $john->id,
        ]);

        $inv5->items()->create([
            'product_id' => $profServices->id,
            'description' => 'Consulting Retainer - 400 hours',
            'quantity' => 400,
            'unit_price' => 150,
            'tax_rate' => 0,
            'total' => 60000,
        ]);

        $inv6 = Invoice::create([
            'invoice_number' => 'INV-0047',
            'customer_id' => $techlogix->id,
            'subtotal' => 35000,
            'tax_amount' => 0,
            'discount' => 0,
            'total' => 35000,
            'paid_amount' => 0,
            'status' => 'draft',
            'due_date' => now()->addDays(78),
            'created_by' => $mike->id,
        ]);

        $inv6->items()->create([
            'product_id' => $profServices->id,
            'description' => 'API Integration Services',
            'quantity' => 233,
            'unit_price' => 150,
            'tax_rate' => 0,
            'total' => 35000,
        ]);
    }
}
