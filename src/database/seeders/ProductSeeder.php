<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Enterprise License',
            'sku' => 'SKU-001',
            'description' => 'Full enterprise software license with premium features',
            'price' => 35000,
            'cost' => 20000,
            'category' => 'Technology',
            'stock' => 50,
            'status' => 'active',
        ]);

        Product::create([
            'name' => 'Professional Services',
            'sku' => 'SKU-002',
            'description' => 'Expert consulting and implementation services',
            'price' => 150,
            'cost' => 75,
            'category' => 'Services',
            'stock' => 999,
            'status' => 'active',
        ]);

        Product::create([
            'name' => 'Cloud Hosting',
            'sku' => 'SKU-003',
            'description' => 'Managed cloud hosting with 99.9% uptime SLA',
            'price' => 500,
            'cost' => 200,
            'category' => 'Technology',
            'stock' => 999,
            'status' => 'active',
        ]);

        Product::create([
            'name' => 'Training Package',
            'sku' => 'SKU-004',
            'description' => 'Comprehensive onboarding and training program',
            'price' => 5000,
            'cost' => 2000,
            'category' => 'Services',
            'stock' => 100,
            'status' => 'active',
        ]);

        Product::create([
            'name' => 'Hardware Kit',
            'sku' => 'SKU-005',
            'description' => 'Essential hardware components and accessories',
            'price' => 2500,
            'cost' => 1500,
            'category' => 'Hardware',
            'stock' => 25,
            'status' => 'active',
        ]);

        Product::create([
            'name' => 'Support Plan',
            'sku' => 'SKU-006',
            'description' => '24/7 priority technical support plan',
            'price' => 1000,
            'cost' => 300,
            'category' => 'Services',
            'stock' => 999,
            'status' => 'active',
        ]);
    }
}
