<?php

namespace Database\Factories;

use App\Models\CmsPage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CmsPage>
 */
class CmsPageFactory extends Factory
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
            'slug' => fake()->slug(2),
            'content' => fake()->paragraphs(3, true),
            'is_active' => true,
            'is_system' => false,
        ];
    }
}
