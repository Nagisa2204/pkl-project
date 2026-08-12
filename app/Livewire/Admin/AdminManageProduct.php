<?php

namespace App\Livewire\Admin;

use App\Enums\StockStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class AdminManageProduct extends Component
{
    use WithFileUploads;

    public ?Product $product = null;
    public ?int $category_id = null;
    public string $name = '';
    public string $slug = '';
    public ?string $description = null;
    public int $min_order_quantity = 1;
    public bool $is_active = true;
    public array $options = [];
    public array $variants = [];
    public array $gallery = [];

    public function mount(?Product $product = null): void
    {
        $this->authorize('admin');
        $this->product = $product?->exists ? $product : null;

        if (! $this->product) {
            $this->category_id = Category::where('is_active', true)->value('id');
            $this->options = [['id' => null, 'name' => '', 'values' => '']];
            $this->variants = [$this->emptyVariant([], true)];
            return;
        }

        $this->product->load(['options.values', 'variants.optionValues.option', 'images']);
        $this->category_id = $this->product->category_id;
        $this->name = $this->product->name;
        $this->slug = $this->product->slug;
        $this->description = $this->product->description;
        $this->min_order_quantity = $this->product->min_order_quantity;
        $this->is_active = $this->product->is_active;
        $this->options = $this->product->options->map(fn ($option) => [
            'id' => $option->id,
            'name' => $option->name,
            'values' => $option->values->pluck('value')->implode(', '),
        ])->values()->all() ?: [['id' => null, 'name' => '', 'values' => '']];
        $this->variants = $this->product->variants->map(fn ($variant) => [
            'id' => $variant->id,
            'label' => $variant->displayName(),
            'selection' => $variant->optionValues->mapWithKeys(fn ($value) => [$value->option->name => $value->value])->all(),
            'sku' => $variant->sku,
            'price' => $variant->price,
            'compare_at_price' => $variant->compare_at_price,
            'stock_quantity' => $variant->stock_quantity,
            'stock_status' => $variant->stock_status->value,
            'weight_grams' => $variant->weight_grams,
            'preorder_days' => $variant->preorder_days,
            'is_default' => $variant->is_default,
            'is_active' => $variant->is_active,
        ])->values()->all();
    }

    public function updatedName(): void
    {
        if (! $this->product) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function addOption(): void
    {
        $this->options[] = ['id' => null, 'name' => '', 'values' => ''];
    }

    public function removeOption(int $index): void
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options);
        $this->generateVariants();
    }

    public function generateVariants(): void
    {
        $this->resetErrorBag('variants');
        $optionSets = $this->normalizedOptionSets();
        $combinations = [[]];

        foreach ($optionSets as $option) {
            $next = [];
            foreach ($combinations as $combination) {
                foreach ($option['values'] as $value) {
                    $next[] = [...$combination, $option['name'] => $value];
                }
            }
            $combinations = $next;
        }

        if ($optionSets === []) {
            $combinations = [[]];
        }

        if (count($combinations) > 100) {
            $this->addError('variants', 'Kombinasi varian melebihi batas 100. Kurangi jumlah opsi atau nilai opsi.');

            return;
        }

        $oldVariants = array_values($this->variants);
        $usedOldIndexes = [];
        $usedSkus = [];
        $generated = [];

        foreach ($combinations as $index => $selection) {
            $sourceIndex = $this->findVariantMatch($oldVariants, $selection, $usedOldIndexes, exact: true)
                ?? $this->findVariantMatch($oldVariants, $selection, $usedOldIndexes)
                ?? $this->findVariantReductionMatch($oldVariants, $selection, $usedOldIndexes);
            $isPreserved = $sourceIndex !== null;

            if ($isPreserved) {
                $variant = $oldVariants[$sourceIndex];
                $usedOldIndexes[] = $sourceIndex;
            } else {
                $templateIndex = $this->findVariantMatch($oldVariants, $selection, [], allowUsed: true);
                $variant = $templateIndex !== null
                    ? $this->cloneVariantForCombination($oldVariants[$templateIndex], $selection, $usedSkus)
                    : $this->emptyVariant($selection, false);
            }

            $label = $selection ? implode(' / ', array_values($selection)) : 'Standar';
            $variant['label'] = $label;
            $variant['selection'] = $selection;
            $variant['is_default'] = $index === 0;

            if (blank($variant['sku'] ?? null) && blank($variant['id'] ?? null)) {
                $variant['sku'] = $this->uniqueSkuSuggestion($selection, $usedSkus);
            }

            if (filled($variant['sku'] ?? null)) {
                $usedSkus[] = mb_strtolower((string) $variant['sku']);
            }

            $generated[] = $variant;
        }

        $this->variants = $generated;
    }

    public function save(): mixed
    {
        $this->authorize('admin');
        $productId = $this->product?->id;
        $rules = [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'alpha_dash', 'max:255', Rule::unique('products')->ignore($productId)],
            'description' => ['nullable', 'string'],
            'min_order_quantity' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'options' => ['array', 'max:5'],
            'options.*.name' => ['nullable', 'string', 'max:100'],
            'options.*.values' => ['nullable', 'string', 'max:1000'],
            'variants' => ['required', 'array', 'min:1', 'max:100'],
            'variants.*.sku' => ['required', 'string', 'max:255', 'distinct'],
            'variants.*.price' => ['required', 'integer', 'min:0'],
            'variants.*.compare_at_price' => ['nullable', 'integer', 'gt:variants.*.price'],
            'variants.*.stock_quantity' => ['required', 'integer', 'min:0'],
            'variants.*.stock_status' => ['required', Rule::enum(StockStatus::class)],
            'variants.*.weight_grams' => ['required', 'integer', 'min:0'],
            'variants.*.preorder_days' => ['required', 'integer', 'min:0'],
            'variants.*.is_active' => ['boolean'],
            'gallery' => ['array', 'max:10'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
        $this->validate($rules);

        $optionNames = collect($this->normalizedOptionSets())->pluck('name');
        if ($optionNames->map(fn ($name) => mb_strtolower($name))->duplicates()->isNotEmpty()) {
            $this->addError('options', 'Nama opsi tidak boleh duplikat.');
        }

        foreach ($this->variants as $index => $variant) {
            $duplicate = ProductVariant::withTrashed()->where('sku', $variant['sku'])
                ->when($variant['id'] ?? null, fn ($query, $id) => $query->where('id', '!=', $id))->exists();
            if ($duplicate) {
                $this->addError("variants.$index.sku", 'SKU sudah digunakan.');
            }
        }
        if ($this->getErrorBag()->isNotEmpty()) {
            return null;
        }

        $product = DB::transaction(function () {
            $product = $this->product ?? new Product;
            $product->fill([
                'category_id' => $this->category_id,
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'min_order_quantity' => $this->min_order_quantity,
                'is_active' => $this->is_active,
            ])->save();

            $optionMap = [];
            $keptOptionIds = [];
            foreach ($this->options as $position => $input) {
                $name = trim((string) ($input['name'] ?? ''));
                $values = collect(explode(',', (string) ($input['values'] ?? '')))->map(fn ($value) => trim($value))->filter()->unique()->values();
                if ($name === '' || $values->isEmpty()) continue;

                $option = $product->options()->updateOrCreate(
                    ['id' => $input['id'] ?? null],
                    ['name' => $name, 'sort_order' => $position]
                );
                $keptOptionIds[] = $option->id;
                $keptValueIds = [];
                foreach ($values as $valuePosition => $valueText) {
                    $value = $option->values()->updateOrCreate(['value' => $valueText], ['sort_order' => $valuePosition]);
                    $keptValueIds[] = $value->id;
                    $optionMap[$name][$valueText] = ['option_id' => $option->id, 'value_id' => $value->id];
                }
                $option->values()->whereNotIn('id', $keptValueIds)->delete();
            }

            $keptVariantIds = [];
            foreach ($this->variants as $position => $input) {
                $selection = $input['selection'] ?? [];
                $pivot = [];
                foreach ($selection as $optionName => $valueText) {
                    $mapped = $optionMap[$optionName][$valueText] ?? null;
                    if (! $mapped) continue;
                    $pivot[$mapped['value_id']] = ['product_option_id' => $mapped['option_id']];
                }
                $key = $pivot ? collect(array_keys($pivot))->sort()->implode('-') : 'default';
                $variant = ProductVariant::withTrashed()->where('product_id', $product->id)
                    ->when($input['id'] ?? null, fn ($query, $id) => $query->whereKey($id), fn ($query) => $query->where('combination_key', $key))
                    ->first() ?? new ProductVariant(['product_id' => $product->id]);
                $variant->fill([
                    'sku' => $input['sku'], 'combination_key' => $key, 'price' => $input['price'],
                    'compare_at_price' => $input['compare_at_price'] ?: null,
                    'stock_quantity' => $input['stock_quantity'], 'stock_status' => $input['stock_status'],
                    'weight_grams' => $input['weight_grams'], 'preorder_days' => $input['preorder_days'],
                    'is_default' => $position === 0, 'is_active' => $input['is_active'] ?? true,
                ])->save();
                if ($variant->trashed()) $variant->restore();
                $variant->optionValues()->sync($pivot);
                $keptVariantIds[] = $variant->id;
            }

            $product->variants()->whereNotIn('id', $keptVariantIds)->delete();
            $staleOptions = $product->options();
            if ($keptOptionIds !== []) {
                $staleOptions->whereNotIn('id', $keptOptionIds);
            }
            $staleOptions->delete();

            $next = (int) ($product->images()->max('sort_order') ?? -1);
            $hasPrimary = $product->images()->where('is_primary', true)->exists();
            foreach ($this->gallery as $image) {
                $path = $image->store('products/gallery', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'alt_text' => $product->name,
                    'is_primary' => ! $hasPrimary,
                    'sort_order' => ++$next,
                ]);
                $hasPrimary = true;
            }

            return $product;
        });

        session()->flash('success', 'Produk dan varian berhasil disimpan.');
        return redirect()->route('admin.products.manage', $product);
    }

    public function setPrimaryImage(int $imageId): void
    {
        $this->authorize('admin');
        abort_unless($this->product?->images()->whereKey($imageId)->exists(), 404);
        DB::transaction(function () use ($imageId) {
            $this->product->images()->update(['is_primary' => false]);
            $this->product->images()->whereKey($imageId)->update(['is_primary' => true]);
        });
        $this->product->load('images');
        $this->dispatch('toast', variant: 'success', message: 'Gambar utama berhasil diperbarui.');
    }

    public function deleteImage(int $imageId): void
    {
        $this->authorize('admin');
        $image = $this->product?->images()->findOrFail($imageId);
        Storage::disk('public')->delete($image->image_path);
        $wasPrimary = $image->is_primary;
        $image->delete();
        if ($wasPrimary) $this->product->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
        $this->product->load('images');
        $this->dispatch('toast', variant: 'success', message: 'Gambar berhasil dihapus.');
    }

    public function delete(): mixed
    {
        $this->authorize('admin');
        $this->product?->delete();
        session()->flash('success', 'Produk dinonaktifkan.');
        return redirect()->route('admin.products');
    }

    private function emptyVariant(array $selection, bool $default): array
    {
        $label = $selection ? implode(' / ', array_values($selection)) : 'Standar';
        return ['id' => null, 'label' => $label, 'selection' => $selection, 'sku' => '', 'price' => 0,
            'compare_at_price' => null, 'stock_quantity' => 0, 'stock_status' => StockStatus::Available->value,
            'weight_grams' => 0, 'preorder_days' => 0, 'is_default' => $default, 'is_active' => true];
    }

    /**
     * @return list<array{name: string, values: list<string>}>
     */
    private function normalizedOptionSets(): array
    {
        return collect($this->options)->map(function (array $option): array {
            $name = trim((string) ($option['name'] ?? ''));
            $values = collect(explode(',', (string) ($option['values'] ?? '')))
                ->map(fn ($value) => trim($value))
                ->filter()
                ->unique(fn ($value) => mb_strtolower($value))
                ->values()
                ->all();

            return compact('name', 'values');
        })->filter(fn (array $option) => $option['name'] !== '' && $option['values'] !== [])->values()->all();
    }

    private function findVariantMatch(
        array $variants,
        array $selection,
        array $usedIndexes,
        bool $exact = false,
        bool $allowUsed = false,
    ): ?int {
        $matches = [];

        foreach ($variants as $index => $variant) {
            if (! $allowUsed && in_array($index, $usedIndexes, true)) {
                continue;
            }

            $oldSelection = (array) ($variant['selection'] ?? []);
            if ($exact && count($oldSelection) !== count($selection)) {
                continue;
            }

            if ($this->selectionIsSubset($oldSelection, $selection)) {
                $matches[$index] = count($oldSelection);
            }
        }

        if ($matches === []) {
            return null;
        }

        arsort($matches);

        return (int) array_key_first($matches);
    }

    private function selectionIsSubset(array $subset, array $selection): bool
    {
        $normalizedSelection = collect($selection)->mapWithKeys(
            fn ($value, $name) => [mb_strtolower(trim((string) $name)) => mb_strtolower(trim((string) $value))]
        );

        foreach ($subset as $name => $value) {
            if ($normalizedSelection->get(mb_strtolower(trim((string) $name))) !== mb_strtolower(trim((string) $value))) {
                return false;
            }
        }

        return true;
    }

    private function findVariantReductionMatch(array $variants, array $selection, array $usedIndexes): ?int
    {
        foreach ($variants as $index => $variant) {
            if (in_array($index, $usedIndexes, true)) {
                continue;
            }

            if ($this->selectionIsSubset($selection, (array) ($variant['selection'] ?? []))) {
                return $index;
            }
        }

        return null;
    }

    private function cloneVariantForCombination(array $source, array $selection, array $usedSkus): array
    {
        $clone = $source;
        $clone['id'] = null;
        $clone['stock_quantity'] = 0;
        $clone['is_default'] = false;
        $clone['is_active'] = false;
        $clone['sku'] = $this->uniqueSkuSuggestion($selection, $usedSkus, (string) ($source['sku'] ?? ''));

        return $clone;
    }

    private function uniqueSkuSuggestion(array $selection, array $usedSkus, string $base = ''): string
    {
        $prefix = Str::upper(Str::slug($base ?: $this->slug ?: $this->name ?: 'VARIAN'));
        $suffix = Str::upper(Str::slug(implode('-', array_values($selection))));
        $candidate = Str::limit(trim($prefix.'-'.$suffix, '-'), 245, '');
        $candidate = $candidate !== '' ? $candidate : 'VARIAN';
        $sequence = 2;

        while (in_array(mb_strtolower($candidate), $usedSkus, true)) {
            $candidate = Str::limit(preg_replace('/-\d+$/', '', $candidate) ?: $candidate, 240, '').'-'.$sequence++;
        }

        return $candidate;
    }

    public function render()
    {
        return view('livewire.admin.admin-manage-product', [
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
