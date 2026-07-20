<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('sku', 50)->nullable()->unique();
            $table->foreignId('unit_id')->constrained();
            $table->decimal('cost_per_unit', 10, 2)->default(0);
            $table->decimal('stock_quantity', 10, 2)->default(0);
            $table->decimal('min_stock_level', 10, 2)->default(0);
            $table->string('category', 50)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
