<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    public function run(): void
    {
        $jane = User::where('email', 'jane@crm.com')->first();
        $john = User::where('email', 'john@crm.com')->first();
        $mike = User::where('email', 'mike@crm.com')->first();

        $acme = Customer::where('name', 'Acme Corp')->first();
        $techlogix = Customer::where('name', 'TechLogix Inc')->first();
        $globex = Customer::where('name', 'Globex LLC')->first();

        $sarahChen = Contact::where('email', 'sarah.chen@acme.com')->first();
        $mikeTorres = Contact::where('email', 'mike.t@globex.com')->first();

        Note::create([
            'title' => 'Meeting notes - Sarah Chen',
            'content' => 'Discussed the Enterprise plan upgrade. Sarah is interested in the premium tier with 24/7 support. Needs to run it by the CFO before making a decision. Follow up next week.',
            'customer_id' => $acme->id,
            'contact_id' => $sarahChen->id,
            'created_by' => $jane->id,
            'is_pinned' => true,
        ]);

        Note::create([
            'title' => 'Technical requirements - Mike Torres',
            'content' => 'Mike outlined the API integration requirements for Globex LLC. They need REST API access, SSO integration, and custom reporting capabilities. Technical team to review and provide a feasibility report.',
            'customer_id' => $globex->id,
            'contact_id' => $mikeTorres->id,
            'created_by' => $mike->id,
            'is_pinned' => false,
        ]);

        Note::create([
            'title' => 'Budget discussion - John Carter',
            'content' => 'John mentioned a budget of $50K for the upcoming fiscal year for software and services. Need to prepare a proposal that fits within this range. He has approval from the board for this expenditure.',
            'customer_id' => $techlogix->id,
            'created_by' => $john->id,
            'is_pinned' => false,
        ]);

        Note::create([
            'title' => 'Onboarding checklist - Acme Corp',
            'content' => 'New customer onboarding checklist for Acme Corp: 1) Set up enterprise account, 2) Configure SSO, 3) Import existing data, 4) Schedule training sessions, 5) Assign dedicated support rep, 6) Set up weekly check-in calls for first month.',
            'customer_id' => $acme->id,
            'created_by' => $jane->id,
            'is_pinned' => true,
        ]);
    }
}
