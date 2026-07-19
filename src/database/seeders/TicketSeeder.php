<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $john = User::where('email', 'john@crm.com')->first();
        $jane = User::where('email', 'jane@crm.com')->first();
        $mike = User::where('email', 'mike@crm.com')->first();
        $sarah = User::where('email', 'sarah@crm.com')->first();
        $admin = User::where('email', 'admin@crm.com')->first();

        $acme = Customer::where('name', 'Acme Corp')->first();
        $techlogix = Customer::where('name', 'TechLogix Inc')->first();
        $globex = Customer::where('name', 'Globex LLC')->first();

        Ticket::create([
            'ticket_number' => 'TKT-001',
            'subject' => 'Login issue',
            'description' => 'User is unable to login to the enterprise portal. Getting an authentication error after entering credentials. Multiple users affected.',
            'priority' => 'urgent',
            'status' => 'open',
            'customer_id' => $acme->id,
            'assigned_to' => $sarah->id,
            'created_by' => $admin->id,
        ]);

        Ticket::create([
            'ticket_number' => 'TKT-002',
            'subject' => 'Feature request',
            'description' => 'TechLogix is requesting a custom reporting dashboard feature with real-time analytics and export to CSV/PDF.',
            'priority' => 'high',
            'status' => 'in_progress',
            'customer_id' => $techlogix->id,
            'assigned_to' => $john->id,
            'created_by' => $admin->id,
        ]);

        Ticket::create([
            'ticket_number' => 'TKT-003',
            'subject' => 'Billing question',
            'description' => 'Globex LLC has a question about the recent invoice. They believe the consulting hours were overcounted and need a detailed breakdown.',
            'priority' => 'medium',
            'status' => 'waiting',
            'customer_id' => $globex->id,
            'assigned_to' => $jane->id,
            'created_by' => $admin->id,
        ]);

        Ticket::create([
            'ticket_number' => 'TKT-004',
            'subject' => 'Data export',
            'description' => 'Acme Corp needs a full data export including customer records, transaction history, and analytics data for their annual audit.',
            'priority' => 'low',
            'status' => 'resolved',
            'customer_id' => $acme->id,
            'assigned_to' => $mike->id,
            'created_by' => $admin->id,
        ]);
    }
}
