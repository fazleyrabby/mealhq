<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->text('instructions')->nullable();
            $table->integer('yield_quantity')->default(1);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->timestamps();

            $table->unique('menu_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
