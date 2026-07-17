<?php

namespace Database\Factories;

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
            'category_id' => fake()->randomNumber(2),
            'slug' => fake()->name(),
            'sku' => fake()->name(),
            'price' => fake()->randomNumber(6),
            'stock_status' => fake()->randomElement(['available', 'unavailable']),
            'stock_quantity' => fake()->randomNumber(2),
            'weight_grams' => fake()->randomNumber(4),
            'is_active' => fake()->boolean(),
        ];
    }
}