<?php

use App\Enums\StockStatus;
use App\Livewire\Admin\AdminManageProduct;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;

test('adding and removing product attributes keeps existing variants incrementally', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create(['slug' => 'kaos-uji']);
    $color = $product->options()->create(['name' => 'Warna', 'sort_order' => 0]);
    $red = $color->values()->create(['value' => 'Merah', 'sort_order' => 0]);
    $blue = $color->values()->create(['value' => 'Biru', 'sort_order' => 1]);

    $redVariant = $product->variants()->create([
        'sku' => 'KAOS-MERAH',
        'combination_key' => (string) $red->id,
        'price' => 100000,
        'stock_quantity' => 10,
        'stock_status' => StockStatus::Available,
        'weight_grams' => 250,
        'is_default' => true,
        'is_active' => true,
    ]);
    $redVariant->optionValues()->attach($red->id, ['product_option_id' => $color->id]);

    $blueVariant = $product->variants()->create([
        'sku' => 'KAOS-BIRU',
        'combination_key' => (string) $blue->id,
        'price' => 110000,
        'stock_quantity' => 20,
        'stock_status' => StockStatus::Available,
        'weight_grams' => 250,
        'is_default' => false,
        'is_active' => true,
    ]);
    $blueVariant->optionValues()->attach($blue->id, ['product_option_id' => $color->id]);

    Livewire::actingAs($admin)
        ->test(AdminManageProduct::class, ['product' => $product])
        ->call('addOption')
        ->set('options.1.name', 'Ukuran')
        ->set('options.1.values', 'M, L')
        ->call('generateVariants')
        ->assertSet('variants', function (array $variants) use ($redVariant, $blueVariant): bool {
            $ids = collect($variants)->pluck('id')->filter()->all();

            return count($variants) === 4
                && in_array($redVariant->id, $ids, true)
                && in_array($blueVariant->id, $ids, true)
                && collect($variants)->whereNull('id')->every(
                    fn (array $variant) => $variant['stock_quantity'] === 0 && $variant['is_active'] === false
                );
        })
        ->call('save')
        ->assertHasNoErrors();

    $product->refresh();
    expect($product->variants()->count())->toBe(4)
        ->and($product->variants()->whereKey($redVariant->id)->exists())->toBeTrue()
        ->and($product->variants()->whereKey($blueVariant->id)->exists())->toBeTrue();

    Livewire::actingAs($admin)
        ->test(AdminManageProduct::class, ['product' => $product])
        ->set('options.1.values', 'M')
        ->call('generateVariants')
        ->assertCount('variants', 2)
        ->call('save')
        ->assertHasNoErrors();

    expect($product->fresh()->variants()->count())->toBe(2)
        ->and($product->fresh()->variants()->whereKey($redVariant->id)->exists())->toBeTrue()
        ->and($product->fresh()->variants()->whereKey($blueVariant->id)->exists())->toBeTrue();
});
