<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'category_id' => Category::factory(),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraph(),
            'min_order_quantity' => 1,
            'is_active' => true,
        ];
    }
}
