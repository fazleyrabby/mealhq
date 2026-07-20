<?php

namespace Database\Factories;

use App\Models\CmsGalleryAlbum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CmsGalleryAlbum>
 */
class CmsGalleryAlbumFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'slug' => fake()->slug(1),
            'description' => fake()->sentence(),
            'sort_order' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}
