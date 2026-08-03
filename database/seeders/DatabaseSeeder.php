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

        $categories = Category::factory()->create([
            'name' => 'Product Physical',  
            'slug' => 'product-physical',      
        ]);
    }
}
