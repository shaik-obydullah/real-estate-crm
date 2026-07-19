<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@crm.com')->first();

        Notification::create([
            'user_id' => $admin->id,
            'title' => 'New lead received',
            'message' => 'A new lead "Enterprise Software" from Acme Corp has been assigned to Jane Smith.',
            'type' => 'info',
            'data' => json_encode(['lead_id' => 1]),
        ]);

        Notification::create([
            'user_id' => $admin->id,
            'title' => 'Invoice INV-0046 overdue',
            'message' => 'Invoice INV-0046 for Globex LLC ($60,000) is overdue. Please follow up with the customer.',
            'type' => 'warning',
            'data' => json_encode(['invoice_number' => 'INV-0046']),
        ]);

        Notification::create([
            'user_id' => $admin->id,
            'title' => 'Task completed by John',
            'message' => 'John Doe has completed the task "Send contract to Globex LLC".',
            'type' => 'success',
            'data' => json_encode(['task_id' => 4]),
        ]);

        Notification::create([
            'user_id' => $admin->id,
            'title' => 'New customer registered',
            'message' => 'A new customer "Clara Nguyen" has been added to the CRM system.',
            'type' => 'info',
            'data' => json_encode(['customer_id' => 7]),
        ]);

        Notification::create([
            'user_id' => $admin->id,
            'title' => 'System update available',
            'message' => 'A new system update (v2.1.0) is available. Please schedule a maintenance window to apply the update.',
            'type' => 'info',
            'data' => null,
        ]);
    }
}
