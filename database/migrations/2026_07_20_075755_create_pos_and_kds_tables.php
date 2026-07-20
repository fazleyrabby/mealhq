<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // POS Drawers
        Schema::create('pos_drawers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('status', ['open', 'closed', 'pending_review'])->default('closed');
            $table->decimal('opening_balance', 10, 2)->default(0);
            $table->decimal('closing_balance', 10, 2)->nullable();
            $table->decimal('expected_balance', 10, 2)->nullable();
            $table->foreignId('opened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // KDS Stations
        Schema::create('kds_stations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('display_name', 100)->nullable();
            $table->enum('type', ['kitchen', 'bar', 'grill', 'prep', 'expo'])->default('kitchen');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('kds_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kds_station_id')->constrained('kds_stations')->cascadeOnDelete();
            $table->enum('status', ['pending', 'preparing', 'ready', 'bumped', 'completed'])->default('pending');
            $table->integer('priority')->default(0);
            $table->integer('prep_time_seconds')->nullable();
            $table->timestamp('bumped_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        // Table Management
        Schema::create('table_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('color', 20)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->nullable()->constrained('table_zones')->nullOnDelete();
            $table->string('table_number', 10);
            $table->integer('capacity')->default(4);
            $table->string('qr_code', 255)->nullable();
            $table->enum('status', ['available', 'occupied', 'reserved', 'cleaning', 'maintenance'])->default('available');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['zone_id', 'table_number']);
        });

        Schema::create('table_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_table_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->integer('guest_count')->default(1);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });

        // Loyalty / CRM
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->integer('points')->default(0);
            $table->string('action', 50);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
        Schema::dropIfExists('table_sessions');
        Schema::dropIfExists('restaurant_tables');
        Schema::dropIfExists('table_zones');
        Schema::dropIfExists('kds_orders');
        Schema::dropIfExists('kds_stations');
        Schema::dropIfExists('pos_drawers');
    }
};
