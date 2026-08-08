<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuotationSeeder extends Seeder
{
    public function run(): void
    {
        $jane = User::where('email', 'jane@crm.com')->first();
        $john = User::where('email', 'john@crm.com')->first();
        $admin = User::where('email', 'admin@crm.com')->first();

        $acme = Customer::where('name', 'Acme Corp')->first();
        $techlogix = Customer::where('name', 'TechLogix Inc')->first();
        $globex = Customer::where('name', 'Globex LLC')->first();

        $enterpriseLicense = Product::where('sku', 'SKU-001')->first();
        $profServices = Product::where('sku', 'SKU-002')->first();
        $cloudHosting = Product::where('sku', 'SKU-003')->first();
        $trainingPkg = Product::where('sku', 'SKU-004')->first();
        $hardwareKit = Product::where('sku', 'SKU-005')->first();
        $supportPlan = Product::where('sku', 'SKU-006')->first();

        $quo1 = Quotation::create([
            'quote_number' => 'QUO-0001',
            'customer_id' => $acme->id,
            'subtotal' => 41000,
            'tax_rate' => 10,
            'tax_amount' => 4100,
            'discount' => 100,
            'total' => 45000,
            'status' => 'sent',
            'valid_until' => now()->addDays(42),
            'payment_terms' => 'Net 30 days',
            'notes' => 'Enterprise software bundle for Acme Corp',
            'created_by' => $admin->id,
        ]);

        $quo1->items()->create([
            'product_id' => $enterpriseLicense->id,
            'description' => 'Enterprise License',
            'quantity' => 1,
            'unit_price' => 35000,
            'tax_rate' => 10,
            'discount' => 0,
            'total' => 35000,
        ]);

        $quo1->items()->create([
            'product_id' => $profServices->id,
            'description' => 'Professional Services - 40 hours',
            'quantity' => 40,
            'unit_price' => 150,
            'tax_rate' => 10,
            'discount' => 0,
            'total' => 6000,
        ]);

        $quo2 = Quotation::create([
            'quote_number' => 'QUO-0002',
            'customer_id' => $techlogix->id,
            'subtotal' => 109000,
            'tax_rate' => 10,
            'tax_amount' => 11000,
            'discount' => 0,
            'total' => 120000,
            'status' => 'draft',
            'valid_until' => now()->addDays(58),
            'payment_terms' => 'Net 45 days',
            'notes' => 'Cloud migration project for TechLogix',
            'created_by' => $john->id,
        ]);

        $quo2->items()->create([
            'product_id' => $cloudHosting->id,
            'description' => 'Cloud Hosting - 12 months',
            'quantity' => 12,
            'unit_price' => 500,
            'tax_rate' => 10,
            'discount' => 0,
            'total' => 6000,
        ]);

        $quo2->items()->create([
            'product_id' => $profServices->id,
            'description' => 'Migration Services - 200 hours',
            'quantity' => 200,
            'unit_price' => 150,
            'tax_rate' => 10,
            'discount' => 0,
            'total' => 30000,
        ]);

        $quo2->items()->create([
            'product_id' => $supportPlan->id,
            'description' => 'Support Plan - 12 months',
            'quantity' => 12,
            'unit_price' => 1000,
            'tax_rate' => 10,
            'discount' => 0,
            'total' => 12000,
        ]);

        $quo3 = Quotation::create([
            'quote_number' => 'QUO-0003',
            'customer_id' => $globex->id,
            'subtotal' => 25500,
            'tax_rate' => 10,
            'tax_amount' => 2750,
            'discount' => 250,
            'total' => 28000,
            'status' => 'accepted',
            'valid_until' => now()->addDays(1),
            'payment_terms' => 'Net 30 days',
            'notes' => 'Security audit services for Globex LLC',
            'created_by' => $jane->id,
        ]);

        $quo3->items()->create([
            'product_id' => $profServices->id,
            'description' => 'Security Audit - 80 hours',
            'quantity' => 80,
            'unit_price' => 150,
            'tax_rate' => 10,
            'discount' => 0,
            'total' => 12000,
        ]);

        $quo3->items()->create([
            'product_id' => $supportPlan->id,
            'description' => 'Post-audit Support - 3 months',
            'quantity' => 3,
            'unit_price' => 1000,
            'tax_rate' => 10,
            'discount' => 0,
            'total' => 3000,
        ]);
    }
}
