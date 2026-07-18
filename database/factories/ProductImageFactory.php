<?php

namespace Database\Factories;

use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'image_path' => 'https://via.placeholder.com/640x480.png',
            'caption' => $this->faker->sentence(),
            'sort_order' => 1,
        ];
    }
}