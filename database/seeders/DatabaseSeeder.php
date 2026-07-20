<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Demo admin user
        User::firstOrCreate(
            ['email' => 'admin@mealhq.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // Demo customer
        Customer::firstOrCreate(
            ['email' => 'customer@mealhq.test'],
            [
                'name' => 'Demo Customer',
                'phone' => '+1234567890',
                'password' => Hash::make('password'),
                'is_active' => true,
                'loyalty_points' => 500,
            ]
        );
    }
}
