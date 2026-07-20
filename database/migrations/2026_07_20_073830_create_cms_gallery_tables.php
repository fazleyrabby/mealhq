<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_gallery_albums', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('description', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('sort_order');
        });

        Schema::create('cms_gallery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained('cms_gallery_albums')->cascadeOnDelete();
            $table->string('title', 150)->nullable();
            $table->string('caption', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_gallery_items');
        Schema::dropIfExists('cms_gallery_albums');
    }
};
