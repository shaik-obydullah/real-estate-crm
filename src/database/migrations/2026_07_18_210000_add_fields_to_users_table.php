<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('password');
            $table->string('department')->nullable()->after('phone');
            $table->enum('role', ['admin', 'manager', 'sales', 'support'])->default('sales')->after('department');
            $table->string('avatar_path')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('avatar_path');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'department',
                'role',
                'avatar_path',
                'is_active',
                'last_login_at',
            ]);
        });
    }
};
