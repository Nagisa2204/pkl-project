<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'phone' => '1234567890',
            'role' => 'admin',
            'is_active' => true,
        ]);

        Category::factory(10)->create();

        Product::factory()->create([
            'name' => 'Test Product',
            'category_id' => 1,
            'slug' => 'test-product',
            'sku' => 'test-product-sku',
            'price' => 10000000,
            'stock_status' => 'available',
            'stock_quantity' => 5,
            'weight_grams' => 6000,
            'is_active' => true,
        ]);
    }
}
