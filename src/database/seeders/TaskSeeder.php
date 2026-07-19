<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $jane = User::where('email', 'jane@crm.com')->first();
        $john = User::where('email', 'john@crm.com')->first();
        $mike = User::where('email', 'mike@crm.com')->first();
        $sarah = User::where('email', 'sarah@crm.com')->first();

        Task::create([
            'title' => 'Review proposal for Acme Corp',
            'description' => 'Review and finalize the enterprise software proposal before sending to the client.',
            'priority' => 'high',
            'status' => 'pending',
            'due_date' => now()->toDateString(),
            'due_time' => '17:00:00',
            'assigned_to' => $john->id,
        ]);

        Task::create([
            'title' => 'Follow up with Sarah Chen',
            'description' => 'Check in regarding the Acme Corp IT infrastructure project status.',
            'priority' => 'medium',
            'status' => 'in_progress',
            'due_date' => now()->addDay()->toDateString(),
            'due_time' => '10:00:00',
            'assigned_to' => $jane->id,
        ]);

        Task::create([
            'title' => 'Prepare demo for TechLogix',
            'description' => 'Set up cloud migration demo environment and prepare presentation materials.',
            'priority' => 'high',
            'status' => 'pending',
            'due_date' => now()->addDays(3)->toDateString(),
            'due_time' => '14:00:00',
            'assigned_to' => $john->id,
        ]);

        Task::create([
            'title' => 'Send contract to Globex LLC',
            'description' => 'Finalize and send the consulting retainer contract to Frank Johnson.',
            'priority' => 'medium',
            'status' => 'completed',
            'due_date' => now()->subDays(1)->toDateString(),
            'assigned_to' => $mike->id,
        ]);

        Task::create([
            'title' => 'Update CRM training docs',
            'description' => 'Update internal documentation with new CRM features and workflows.',
            'priority' => 'low',
            'status' => 'pending',
            'due_date' => now()->addWeek()->toDateString(),
            'assigned_to' => $sarah->id,
        ]);

        Task::create([
            'title' => 'Schedule meeting with Elena',
            'description' => 'Set up a consultation meeting with Elena Martinez for the Globex security audit project.',
            'priority' => 'medium',
            'status' => 'pending',
            'due_date' => now()->addDays(2)->toDateString(),
            'due_time' => '11:00:00',
            'assigned_to' => $jane->id,
        ]);
    }
}
