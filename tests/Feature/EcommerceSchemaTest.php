<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\CartService;

test('generic product variants are the cart source of truth', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create();
    $option = $product->options()->create(['name' => 'Kapasitas']);
    $value = $option->values()->create(['value' => '256 GB']);
    $variant = $product->variants()->create([
        'sku' => 'PHONE-256', 'combination_key' => (string) $value->id,
        'price' => 5000000, 'stock_quantity' => 3, 'stock_status' => 'available',
        'weight_grams' => 300, 'is_default' => true, 'is_active' => true,
    ]);
    $variant->optionValues()->attach($value->id, ['product_option_id' => $option->id]);

    app(CartService::class)->add($user, $variant->id, 2);

    $item = CartItem::with('variant.optionValues')->firstOrFail();
    expect($item->product_variant_id)->toBe($variant->id)
        ->and($item->price)->toBe(5000000)
        ->and($item->quantity)->toBe(2)
        ->and($item->variant->optionValues->first()->value)->toBe('256 GB');
});

test('a user cannot modify another users cart item', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();
    $variant = Product::factory()->create()->variants()->create([
        'sku' => 'SECURE-SKU', 'combination_key' => 'default', 'price' => 10000,
        'stock_quantity' => 5, 'stock_status' => 'available', 'weight_grams' => 100,
        'is_default' => true, 'is_active' => true,
    ]);
    $item = CartItem::create([
        'cart_id' => Cart::create(['user_id' => $owner->id])->id,
        'product_variant_id' => $variant->id,
        'quantity' => 1,
        'price' => $variant->price,
    ]);

    expect(fn () => app(CartService::class)->remove($attacker, $item->id))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});
