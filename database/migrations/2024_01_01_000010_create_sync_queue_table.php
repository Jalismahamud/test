<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sync_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->string('device_id', 100);
            $table->string('entity_type', 50);
            $table->uuid('entity_uuid');
            $table->json('payload');
            $table->enum('status', ['pending', 'processing', 'synced', 'failed'])->default('pending');
            $table->integer('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            $table->index(['business_id', 'status']);
            $table->index('entity_uuid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_queue');
    }
};
