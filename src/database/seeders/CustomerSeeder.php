<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $jane = User::where('email', 'jane@crm.com')->first();
        $john = User::where('email', 'john@crm.com')->first();

        Customer::create([
            'name' => 'John Doe',
            'type' => 'individual',
            'email' => 'john@example.com',
            'phone' => '+1-555-0101',
            'city' => 'New York',
            'state' => 'NY',
            'country' => 'USA',
            'credit_limit' => 50000,
            'status' => 'active',
            'account_manager_id' => $jane->id,
        ]);

        Customer::create([
            'name' => 'Acme Corp',
            'type' => 'company',
            'email' => 'info@acme.com',
            'phone' => '+1-555-0102',
            'industry' => 'Technology',
            'website' => 'https://acme.com',
            'address' => '123 Main St',
            'city' => 'San Francisco',
            'state' => 'CA',
            'country' => 'USA',
            'credit_limit' => 200000,
            'status' => 'active',
            'account_manager_id' => $john->id,
        ]);

        Customer::create([
            'name' => 'Sarah Wilson',
            'type' => 'individual',
            'email' => 'sarah@example.com',
            'phone' => '+1-555-0103',
            'city' => 'Chicago',
            'state' => 'IL',
            'country' => 'USA',
            'credit_limit' => 10000,
            'status' => 'inactive',
            'account_manager_id' => $jane->id,
        ]);

        Customer::create([
            'name' => 'TechLogix Inc',
            'type' => 'company',
            'email' => 'contact@techlogix.io',
            'phone' => '+1-555-0104',
            'industry' => 'Software',
            'website' => 'https://techlogix.io',
            'address' => '456 Tech Blvd',
            'city' => 'Austin',
            'state' => 'TX',
            'country' => 'USA',
            'credit_limit' => 150000,
            'status' => 'active',
            'account_manager_id' => $john->id,
        ]);

        Customer::create([
            'name' => 'Globex LLC',
            'type' => 'company',
            'email' => 'info@globex.com',
            'phone' => '+1-555-0105',
            'industry' => 'Manufacturing',
            'website' => 'https://globex.com',
            'address' => '789 Industry Ave',
            'city' => 'Detroit',
            'state' => 'MI',
            'country' => 'USA',
            'credit_limit' => 100000,
            'status' => 'active',
            'account_manager_id' => $jane->id,
        ]);

        Customer::create([
            'name' => 'Mike Keller',
            'type' => 'individual',
            'email' => 'mike@example.com',
            'phone' => '+1-555-0106',
            'city' => 'Seattle',
            'state' => 'WA',
            'country' => 'USA',
            'credit_limit' => 5000,
            'status' => 'archived',
        ]);

        Customer::create([
            'name' => 'Clara Nguyen',
            'type' => 'individual',
            'email' => 'clara@example.com',
            'phone' => '+1-555-0107',
            'city' => 'Portland',
            'state' => 'OR',
            'country' => 'USA',
            'credit_limit' => 25000,
            'status' => 'active',
            'account_manager_id' => $john->id,
        ]);

        Customer::create([
            'name' => 'Elena Martinez',
            'type' => 'individual',
            'email' => 'elena@example.com',
            'phone' => '+1-555-0108',
            'city' => 'Miami',
            'state' => 'FL',
            'country' => 'USA',
            'credit_limit' => 30000,
            'status' => 'active',
            'account_manager_id' => $jane->id,
        ]);
    }
}
