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
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('slug', 150)->unique();
            $table->longText('content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->string('meta_title', 150)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('is_active');
        });

        Schema::create('cms_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('cms_pages')->cascadeOnDelete();
            $table->string('section_key', 50);
            $table->string('title', 150)->nullable();
            $table->string('subtitle', 255)->nullable();
            $table->text('body_content')->nullable();
            $table->string('cta_link', 255)->nullable();
            $table->string('cta_text', 50)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->unique(['page_id', 'section_key']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_sections');
        Schema::dropIfExists('cms_pages');
    }
};
