<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('company_name')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->enum('source', [
                'website', 'referral', 'social_media', 'email_campaign',
                'cold_call', 'partner', 'event', 'other',
            ]);
            $table->enum('status', [
                'new', 'contacted', 'qualified', 'proposal_sent',
                'negotiation', 'won', 'lost',
            ])->default('new');
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->decimal('value', 12, 2)->default(0);
            $table->date('expected_closing_date')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('priority');
            $table->index('source');
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
