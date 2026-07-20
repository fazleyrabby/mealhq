<?php

namespace Database\Factories;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => CategoryFactory::new(),
            'name' => fake()->words(3, true),
            'slug' => fake()->slug(3),
            'description' => fake()->sentence(),
            'base_price' => fake()->randomFloat(2, 5, 30),
            'is_active' => true,
        ];
    }
}
