<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            SettingSeeder::class,
            UserSeeder::class,
            CustomerSeeder::class,
            ContactSeeder::class,
            TagSeeder::class,
            LeadSeeder::class,
            OpportunitySeeder::class,
            ProductSeeder::class,
            QuotationSeeder::class,
            SalesOrderSeeder::class,
            InvoiceSeeder::class,
            PaymentSeeder::class,
            TaskSeeder::class,
            ActivitySeeder::class,
            NoteSeeder::class,
            TicketSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
