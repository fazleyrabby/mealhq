<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_item_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->decimal('price_adjustment', 10, 2)->default(0);
            $table->string('sku', 50)->nullable()->unique();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('menu_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_item_variants');
    }
};
