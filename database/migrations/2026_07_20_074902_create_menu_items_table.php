<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->text('description')->nullable();
            $table->text('ingredients')->nullable();
            $table->string('image_url', 255)->nullable();
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('prep_time_minutes')->nullable();
            $table->integer('calories')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('has_variants')->default(false);
            $table->enum('channel_visibility', ['all', 'web_only', 'pos_only', 'qr_only'])->default('all');
            $table->enum('unit_type', ['each', 'per_plate', 'per_kg', 'per_liter', 'per_dozen'])->default('each');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
