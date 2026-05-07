<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->string('name');
            $table->string('sku', 100)->unique();
            $table->string('barcode', 100)->nullable();
            $table->text('description')->nullable();
            $table->decimal('cost_price', 15, 2);
            $table->decimal('selling_price', 15, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->string('unit', 20)->default('pcs');
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('track_inventory')->default(true);
            $table->integer('alert_quantity')->default(5);
            $table->timestamps();
            $table->index(['business_id', 'is_active']);
            $table->index(['business_id', 'category_id']);
            $table->fullText(['name', 'sku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
