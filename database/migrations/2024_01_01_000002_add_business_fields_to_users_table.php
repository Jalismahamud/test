<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->after('id')->constrained('businesses')->nullOnDelete();
            $table->enum('role', ['admin', 'manager', 'cashier'])->default('cashier')->after('email');
            $table->string('avatar')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('avatar');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->index(['business_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->dropColumn(['business_id', 'role', 'avatar', 'is_active', 'last_login_at']);
        });
    }
};
