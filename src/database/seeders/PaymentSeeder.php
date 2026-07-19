<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $jane = User::where('email', 'jane@crm.com')->first();
        $john = User::where('email', 'john@crm.com')->first();

        $acme = Customer::where('name', 'Acme Corp')->first();
        $globex = Customer::where('name', 'Globex LLC')->first();

        $inv42 = Invoice::where('invoice_number', 'INV-0042')->first();
        $inv44 = Invoice::where('invoice_number', 'INV-0044')->first();

        Payment::create([
            'payment_number' => 'PAY-0001',
            'invoice_id' => $inv42->id,
            'customer_id' => $acme->id,
            'amount' => 43100,
            'method' => 'bank_transfer',
            'reference_number' => 'BT-2026-001',
            'payment_date' => now()->subDays(22),
            'notes' => 'Full payment for INV-0042',
            'status' => 'completed',
            'created_by' => $jane->id,
        ]);

        Payment::create([
            'payment_number' => 'PAY-0002',
            'invoice_id' => $inv44->id,
            'customer_id' => $globex->id,
            'amount' => 14000,
            'method' => 'credit_card',
            'reference_number' => 'CC-2026-002',
            'payment_date' => now()->subDays(5),
            'notes' => 'Partial payment for INV-0044',
            'status' => 'completed',
            'created_by' => $john->id,
        ]);
    }
}
