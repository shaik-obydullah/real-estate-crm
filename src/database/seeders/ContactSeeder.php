<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $acme = Customer::where('name', 'Acme Corp')->first();
        $globex = Customer::where('name', 'Globex LLC')->first();
        $techlogix = Customer::where('name', 'TechLogix Inc')->first();

        Contact::create([
            'customer_id' => $acme->id,
            'first_name' => 'Alice',
            'last_name' => 'Liu',
            'email' => 'alice@acme.com',
            'phone' => '+1-555-1001',
            'position' => 'VP Engineering',
            'is_primary' => true,
        ]);

        Contact::create([
            'customer_id' => $acme->id,
            'first_name' => 'Bob',
            'last_name' => 'Richards',
            'email' => 'bob@acme.com',
            'phone' => '+1-555-1002',
            'position' => 'CTO',
            'is_primary' => false,
        ]);

        Contact::create([
            'customer_id' => $globex->id,
            'first_name' => 'Frank',
            'last_name' => 'Johnson',
            'email' => 'frank@globex.com',
            'phone' => '+1-555-1003',
            'position' => 'Director',
            'is_primary' => true,
        ]);

        Contact::create([
            'customer_id' => $techlogix->id,
            'first_name' => 'David',
            'last_name' => 'Kim',
            'email' => 'david@techlogix.io',
            'phone' => '+1-555-1004',
            'position' => 'CEO',
            'is_primary' => true,
        ]);

        Contact::create([
            'customer_id' => $techlogix->id,
            'first_name' => 'Emily',
            'last_name' => 'Park',
            'email' => 'emily@techlogix.io',
            'phone' => '+1-555-1005',
            'position' => 'CFO',
            'is_primary' => false,
        ]);

        Contact::create([
            'customer_id' => $techlogix->id,
            'first_name' => 'Clara',
            'last_name' => 'Nguyen',
            'email' => 'clara@techlogix.io',
            'phone' => '+1-555-1006',
            'position' => 'VP Sales',
            'is_primary' => false,
        ]);

        Contact::create([
            'customer_id' => $acme->id,
            'first_name' => 'Sarah',
            'last_name' => 'Chen',
            'email' => 'sarah.chen@acme.com',
            'phone' => '+1-555-1007',
            'position' => 'Head of IT',
            'is_primary' => false,
        ]);

        Contact::create([
            'customer_id' => $globex->id,
            'first_name' => 'Mike',
            'last_name' => 'Torres',
            'email' => 'mike.t@globex.com',
            'phone' => '+1-555-1008',
            'position' => 'Manager',
            'is_primary' => false,
        ]);
    }
}
