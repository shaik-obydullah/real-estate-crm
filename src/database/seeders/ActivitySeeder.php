<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $jane = User::where('email', 'jane@crm.com')->first();
        $john = User::where('email', 'john@crm.com')->first();
        $mike = User::where('email', 'mike@crm.com')->first();

        Activity::create([
            'type' => 'call',
            'title' => 'John Carter moved to Proposal',
            'description' => 'Discussed proposal details with John Carter. He is ready to move forward.',
            'date' => now()->subHours(2)->toDateString(),
            'time' => now()->subHours(2)->format('H:i:s'),
            'duration' => 15,
            'outcome' => 'Proposal sent',
            'assigned_to' => $john->id,
            'created_by' => $john->id,
        ]);

        Activity::create([
            'type' => 'meeting',
            'title' => 'Sarah Chen signed contract',
            'description' => 'Met with Sarah Chen to sign the enterprise software contract.',
            'date' => now()->subHours(4)->toDateString(),
            'time' => now()->subHours(4)->format('H:i:s'),
            'duration' => 60,
            'outcome' => 'Contract signed',
            'assigned_to' => $jane->id,
            'created_by' => $jane->id,
        ]);

        Activity::create([
            'type' => 'email',
            'title' => 'Mike Torres requested demo',
            'description' => 'Mike Torres sent an email requesting a product demo for Globex LLC.',
            'date' => now()->subDay()->toDateString(),
            'time' => '14:30:00',
            'outcome' => 'Demo scheduled',
            'assigned_to' => $mike->id,
            'created_by' => $mike->id,
        ]);

        Activity::create([
            'type' => 'note',
            'title' => 'Emily Park added note',
            'description' => 'Emily Park shared budget information for the upcoming TechLogix project.',
            'date' => now()->subDay()->toDateString(),
            'time' => '09:15:00',
            'assigned_to' => $john->id,
            'created_by' => $john->id,
        ]);

        Activity::create([
            'type' => 'call',
            'title' => 'David Wu assigned lead',
            'description' => 'Received lead assignment notification from David Wu for new enterprise deal.',
            'date' => now()->subDays(2)->toDateString(),
            'time' => '11:00:00',
            'duration' => 10,
            'outcome' => 'Lead assigned',
            'assigned_to' => $jane->id,
            'created_by' => $jane->id,
        ]);

        Activity::create([
            'type' => 'meeting',
            'title' => 'Alice Liu consultation',
            'description' => 'Initial consultation with Alice Liu regarding enterprise software needs at Acme Corp.',
            'date' => now()->subDays(3)->toDateString(),
            'time' => '15:00:00',
            'duration' => 45,
            'outcome' => 'Requirements gathered',
            'assigned_to' => $john->id,
            'created_by' => $john->id,
        ]);

        Activity::create([
            'type' => 'email',
            'title' => 'Frank Johnson follow-up',
            'description' => 'Sent follow-up email to Frank Johnson regarding the consulting retainer proposal.',
            'date' => now()->subDays(4)->toDateString(),
            'time' => '10:30:00',
            'outcome' => 'Awaiting response',
            'assigned_to' => $mike->id,
            'created_by' => $mike->id,
        ]);

        Activity::create([
            'type' => 'call',
            'title' => 'Bob Richards check-in',
            'description' => 'Quarterly check-in call with Bob Richards at Acme Corp to discuss support needs.',
            'date' => now()->subDays(5)->toDateString(),
            'time' => '13:00:00',
            'duration' => 20,
            'outcome' => 'Satisfied with service',
            'assigned_to' => $jane->id,
            'created_by' => $jane->id,
        ]);
    }
}
