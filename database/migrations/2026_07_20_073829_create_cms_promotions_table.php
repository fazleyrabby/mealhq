<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cms_promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('subtitle', 255)->nullable();
            $table->string('promo_code', 30)->nullable();
            $table->enum('discount_type', ['percentage', 'fixed', 'bogo', 'free_delivery'])->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('cta_url', 255)->nullable();
            $table->string('cta_text', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['start_date', 'end_date']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_promotions');
    }
};
