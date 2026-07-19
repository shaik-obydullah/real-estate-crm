<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Enterprise', 'color' => '#3B82F6'],
            ['name' => 'Small Business', 'color' => '#10B981'],
            ['name' => 'High Priority', 'color' => '#EF4444'],
            ['name' => 'VIP', 'color' => '#EAB308'],
            ['name' => 'Technology', 'color' => '#8B5CF6'],
            ['name' => 'Healthcare', 'color' => '#6366F1'],
            ['name' => 'Finance', 'color' => '#EC4899'],
            ['name' => 'Retail', 'color' => '#F97316'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
