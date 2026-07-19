<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $jane = User::where('email', 'jane@crm.com')->first();
        $john = User::where('email', 'john@crm.com')->first();
        $mike = User::where('email', 'mike@crm.com')->first();

        Lead::create([
            'title' => 'Enterprise Software',
            'company_name' => 'Acme Corp',
            'contact_name' => 'Alice Liu',
            'contact_email' => 'alice@acme.com',
            'source' => 'referral',
            'status' => 'new',
            'priority' => 'high',
            'value' => 45000,
            'expected_closing_date' => now()->addDays(60),
            'assigned_to' => $jane->id,
        ]);

        Lead::create([
            'title' => 'Cloud Migration',
            'company_name' => 'TechLogix Inc',
            'contact_name' => 'Clara Nguyen',
            'contact_email' => 'clara@techlogix.io',
            'source' => 'website',
            'status' => 'contacted',
            'priority' => 'high',
            'value' => 120000,
            'expected_closing_date' => now()->addDays(90),
            'assigned_to' => $john->id,
        ]);

        Lead::create([
            'title' => 'Security Audit',
            'company_name' => 'Globex LLC',
            'contact_name' => 'Elena Martinez',
            'contact_email' => 'elena@example.com',
            'source' => 'email_campaign',
            'status' => 'qualified',
            'priority' => 'medium',
            'value' => 28000,
            'expected_closing_date' => now()->addDays(45),
            'assigned_to' => $jane->id,
        ]);

        Lead::create([
            'title' => 'API Integration',
            'company_name' => 'TechLogix Inc',
            'contact_name' => 'David Kim',
            'contact_email' => 'david@techlogix.io',
            'source' => 'partner',
            'status' => 'proposal_sent',
            'priority' => 'medium',
            'value' => 35000,
            'expected_closing_date' => now()->addDays(30),
            'assigned_to' => $mike->id,
        ]);

        Lead::create([
            'title' => 'Consulting Retainer',
            'company_name' => 'Globex LLC',
            'contact_name' => 'Frank Johnson',
            'contact_email' => 'frank@globex.com',
            'source' => 'social_media',
            'status' => 'negotiation',
            'priority' => 'high',
            'value' => 60000,
            'expected_closing_date' => now()->addDays(20),
            'assigned_to' => $john->id,
        ]);

        Lead::create([
            'title' => 'Hardware Upgrade',
            'company_name' => 'Acme Corp',
            'contact_name' => 'Bob Richards',
            'contact_email' => 'bob@acme.com',
            'source' => 'cold_call',
            'status' => 'won',
            'priority' => 'low',
            'value' => 15000,
            'assigned_to' => $jane->id,
        ]);

        Lead::create([
            'title' => 'IT Support',
            'company_name' => 'TechLogix Inc',
            'contact_name' => 'David Kim',
            'contact_email' => 'david@techlogix.io',
            'source' => 'event',
            'status' => 'lost',
            'priority' => 'low',
            'value' => 10000,
            'assigned_to' => $john->id,
        ]);

        Lead::create([
            'title' => 'Website Redesign',
            'company_name' => 'Globex LLC',
            'contact_name' => 'Elena Martinez',
            'contact_email' => 'elena@example.com',
            'source' => 'website',
            'status' => 'new',
            'priority' => 'medium',
            'value' => 12000,
            'expected_closing_date' => now()->addDays(75),
            'assigned_to' => $jane->id,
        ]);
    }
}
