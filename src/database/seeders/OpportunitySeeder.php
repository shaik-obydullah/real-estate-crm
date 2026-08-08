<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Seeder;

class OpportunitySeeder extends Seeder
{
    public function run(): void
    {
        $jane = User::where('email', 'jane@crm.com')->first();
        $john = User::where('email', 'john@crm.com')->first();
        $mike = User::where('email', 'mike@crm.com')->first();

        $davidKim = Contact::where('email', 'david@techlogix.io')->first();
        $aliceLiu = Contact::where('email', 'alice@acme.com')->first();
        $frankJohnson = Contact::where('email', 'frank@globex.com')->first();
        $bobRichards = Contact::where('email', 'bob@acme.com')->first();
        $claraNguyen = Contact::where('email', 'clara@techlogix.io')->first();
        $mikeTorres = Contact::where('email', 'mike.t@globex.com')->first();

        Opportunity::create([
            'name' => 'Cloud Migration',
            'company_name' => 'TechLogix Inc',
            'contact_id' => $claraNguyen->id,
            'value' => 120000,
            'stage' => 'qualified',
            'probability' => 50,
            'expected_closing_date' => now()->addDays(90),
            'assigned_to' => $john->id,
        ]);

        Opportunity::create([
            'name' => 'Security Audit',
            'company_name' => 'Globex LLC',
            'contact_id' => $frankJohnson->id,
            'value' => 28000,
            'stage' => 'qualified',
            'probability' => 50,
            'expected_closing_date' => now()->addDays(45),
            'assigned_to' => $jane->id,
        ]);

        Opportunity::create([
            'name' => 'ERP System',
            'company_name' => 'Acme Corp',
            'contact_id' => $aliceLiu->id,
            'value' => 75000,
            'stage' => 'meeting',
            'probability' => 65,
            'expected_closing_date' => now()->addDays(60),
            'assigned_to' => $john->id,
        ]);

        Opportunity::create([
            'name' => 'Mobile App',
            'company_name' => 'TechLogix Inc',
            'contact_id' => $davidKim->id,
            'value' => 18000,
            'stage' => 'meeting',
            'probability' => 55,
            'expected_closing_date' => now()->addDays(40),
            'assigned_to' => $mike->id,
        ]);

        Opportunity::create([
            'name' => 'API Integration',
            'company_name' => 'TechLogix Inc',
            'contact_id' => $davidKim->id,
            'value' => 35000,
            'stage' => 'proposal',
            'probability' => 70,
            'expected_closing_date' => now()->addDays(30),
            'assigned_to' => $mike->id,
        ]);

        Opportunity::create([
            'name' => 'Data Analytics',
            'company_name' => 'Globex LLC',
            'contact_id' => $mikeTorres->id,
            'value' => 22000,
            'stage' => 'proposal',
            'probability' => 75,
            'expected_closing_date' => now()->addDays(35),
            'assigned_to' => $jane->id,
        ]);

        Opportunity::create([
            'name' => 'Consulting Retainer',
            'company_name' => 'Globex LLC',
            'contact_id' => $frankJohnson->id,
            'value' => 60000,
            'stage' => 'negotiation',
            'probability' => 85,
            'expected_closing_date' => now()->addDays(20),
            'assigned_to' => $john->id,
        ]);

        Opportunity::create([
            'name' => 'Training Program',
            'company_name' => 'Acme Corp',
            'contact_id' => $bobRichards->id,
            'value' => 30000,
            'stage' => 'negotiation',
            'probability' => 80,
            'expected_closing_date' => now()->addDays(15),
            'assigned_to' => $jane->id,
        ]);

        Opportunity::create([
            'name' => 'Hardware Upgrade',
            'company_name' => 'Acme Corp',
            'contact_id' => $bobRichards->id,
            'value' => 15000,
            'stage' => 'won',
            'probability' => 100,
            'expected_closing_date' => now()->subDays(10),
            'assigned_to' => $jane->id,
        ]);

        Opportunity::create([
            'name' => 'IT Support',
            'company_name' => 'TechLogix Inc',
            'contact_id' => $davidKim->id,
            'value' => 10000,
            'stage' => 'lost',
            'probability' => 0,
            'expected_closing_date' => now()->subDays(5),
            'assigned_to' => $john->id,
        ]);
    }
}
