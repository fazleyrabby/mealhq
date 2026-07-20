<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('group', 50)->default('general');
            $table->timestamps();
        });

        // Seed default settings
        $now = now();
        $defaults = [
            ['key' => 'company_name', 'value' => 'MealHQ', 'group' => 'company', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'company_email', 'value' => 'info@restaurant.test', 'group' => 'company', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'company_phone', 'value' => '+1234567890', 'group' => 'company', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'company_address', 'value' => '123 Main St, City', 'group' => 'company', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'company_currency', 'value' => 'USD', 'group' => 'company', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'company_timezone', 'value' => 'UTC', 'group' => 'company', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'min_order_amount', 'value' => '0', 'group' => 'orders', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'delivery_radius', 'value' => '10', 'group' => 'orders', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'reservation_duration', 'value' => '90', 'group' => 'reservations', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'reservation_max_days_advance', 'value' => '30', 'group' => 'reservations', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'service_charge_percentage', 'value' => '0', 'group' => 'billing', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'enable_delivery', 'value' => '1', 'group' => 'orders', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'enable_takeaway', 'value' => '1', 'group' => 'orders', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'enable_online_ordering', 'value' => '1', 'group' => 'orders', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'enable_reservations', 'value' => '1', 'group' => 'reservations', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('settings')->insert($defaults);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
