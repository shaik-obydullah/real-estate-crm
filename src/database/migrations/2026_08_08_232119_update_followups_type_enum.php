<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('followups', function (Blueprint $table) {
            $table->enum('type', ['call', 'meeting', 'email', 'other', 'follow_up', 'reminder'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('followups', function (Blueprint $table) {
            $table->enum('type', ['call', 'meeting', 'email', 'other'])->change();
        });
    }
};
