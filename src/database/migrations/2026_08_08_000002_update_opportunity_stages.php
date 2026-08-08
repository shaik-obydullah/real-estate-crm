<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            $table->enum('stage', [
                'prospecting', 'qualification', 'needs_analysis', 'proposal',
                'negotiation', 'closed_won', 'closed_lost', 'new', 'qualified', 'meeting', 'won', 'lost',
            ])->default('prospecting')->change();
        });

        DB::table('opportunities')->update([
            'stage' => DB::raw("CASE stage
                WHEN 'prospecting' THEN 'new'
                WHEN 'qualification' THEN 'qualified'
                WHEN 'needs_analysis' THEN 'meeting'
                WHEN 'closed_won' THEN 'won'
                WHEN 'closed_lost' THEN 'lost'
                ELSE stage END"),
        ]);

        Schema::table('opportunities', function (Blueprint $table) {
            $table->enum('stage', [
                'new', 'qualified', 'meeting', 'proposal',
                'negotiation', 'won', 'lost',
            ])->default('new')->change();
        });
    }

    public function down(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            $table->enum('stage', [
                'new', 'qualified', 'meeting', 'proposal', 'negotiation', 'won', 'lost',
                'prospecting', 'qualification', 'needs_analysis', 'closed_won', 'closed_lost',
            ])->default('new')->change();
        });

        DB::table('opportunities')->update([
            'stage' => DB::raw("CASE stage
                WHEN 'new' THEN 'prospecting'
                WHEN 'qualified' THEN 'qualification'
                WHEN 'meeting' THEN 'needs_analysis'
                WHEN 'won' THEN 'closed_won'
                WHEN 'lost' THEN 'closed_lost'
                ELSE stage END"),
        ]);

        Schema::table('opportunities', function (Blueprint $table) {
            $table->enum('stage', [
                'prospecting', 'qualification', 'needs_analysis', 'proposal',
                'negotiation', 'closed_won', 'closed_lost',
            ])->default('prospecting')->change();
        });
    }
};
