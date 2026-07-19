<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('value', 12, 2)->default(0);
            $table->enum('stage', [
                'prospecting', 'qualification', 'needs_analysis', 'proposal',
                'negotiation', 'closed_won', 'closed_lost',
            ])->default('prospecting');
            $table->integer('probability')->default(0);
            $table->date('expected_closing_date')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('stage');
            $table->index('assigned_to');
            $table->index('contact_id');
            $table->index('lead_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
