<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_hours', function (Blueprint $table) {
            $table->id();
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });

        // Default business hours (Mon-Sat 9:00-22:00, Sun closed)
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $now = now();
        foreach ($days as $i => $day) {
            DB::table('business_hours')->insert([
                'day_of_week' => $day,
                'opening_time' => $i === 6 ? null : '09:00:00',
                'closing_time' => $i === 6 ? null : '22:00:00',
                'is_closed' => $i === 6,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('business_hours');
    }
};
