<?php

namespace Database\Seeders;

use App\Enums\StockStatus;
use App\Models\Category;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\StoreSetting;
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
            'name' => 'Laptops',
            'slug' => 'laptops',
        ]);

        StoreSetting::create([
            'key' => 'default',
            'store_name' => 'Demo Ecommerce',
            'email' => 'admin@example.com',
            'whatsapp' => '081234567890',
            'city' => 'Mataram',
            'province' => 'Nusa Tenggara Barat',
        ]);

        $product = Product::factory()->create([
            'category_id' => $categories->id,
            'name' => 'Acer Aspire 5 Slim Laptop',
            'slug' => 'acer-aspire-5-slim-laptop',
            'description' => 'Acer Aspire 5 Slim Laptop, 15.6 inches Full HD IPS Display, AMD Ryzen 3 3200U, Vega 3 Graphics, 4GB DDR4, 128GB SSD, Backlit Keyboard, Windows 10 in S Mode, A515-43-R19L, Silver',
            'min_order_quantity' => 1,
            'is_active' => true
        ]);

        $ram = $product->options()->create(['name' => 'RAM', 'sort_order' => 0]);
        $ram8 = $ram->values()->create(['value' => '8 GB', 'sort_order' => 0]);
        $ram16 = $ram->values()->create(['value' => '16 GB', 'sort_order' => 1]);

        foreach ([[$ram8, 'ACER-ASP-8', 6999000], [$ram16, 'ACER-ASP-16', 7999000]] as $index => [$value, $sku, $price]) {
            $variant = $product->variants()->create([
                'sku' => $sku,
                'combination_key' => (string) $value->id,
                'price' => $price,
                'stock_quantity' => 25,
                'stock_status' => StockStatus::Available,
                'weight_grams' => 1800,
                'is_default' => $index === 0,
                'is_active' => true,
            ]);
            $variant->optionValues()->attach($value->id, ['product_option_id' => $ram->id]);
        }

        ProductImage::factory()->count(3)->create(
            ['product_id' => $product->id]
        );

        $product->images()->orderBy('id')->first()?->update(['is_primary' => true]);
    }
}
