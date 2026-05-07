<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('cashier_id')->constrained('users');
            $table->string('invoice_number', 50)->unique();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2);
            $table->decimal('change_amount', 15, 2)->default(0);
            $table->enum('payment_method', ['cash', 'card', 'mobile', 'mixed'])->default('cash');
            $table->enum('status', ['completed', 'held', 'refunded'])->default('completed');
            $table->text('note')->nullable();
            $table->timestamp('sold_at');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            $table->index(['business_id', 'created_at']);
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'cashier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
