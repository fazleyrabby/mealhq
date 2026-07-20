<?php

namespace Database\Factories;

use App\Models\CmsPromotion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CmsPromotion>
 */
class CmsPromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'subtitle' => fake()->sentence(),
            'promo_code' => strtoupper(fake()->bothify('??###')),
            'discount_type' => fake()->randomElement(['percentage', 'fixed']),
            'discount_value' => fake()->randomFloat(2, 5, 50),
            'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'end_date' => fake()->dateTimeBetween('now', '+3 months'),
            'is_active' => true,
        ];
    }
}
