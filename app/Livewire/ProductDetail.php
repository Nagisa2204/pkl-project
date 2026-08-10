<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductDetail extends Component
{
    public Product $product;
    public array $selectedOptions = [];
    public ?int $selectedVariantId = null;
    public int $quantity = 1;

    public function mount(string $slug): void
    {
        $this->product = Product::query()
            ->with(['images', 'category', 'options.values', 'activeVariants.optionValues'])
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $default = $this->product->activeVariants->firstWhere('is_default', true)
            ?? ($this->product->activeVariants->count() === 1 ? $this->product->activeVariants->first() : null);

        if ($default) {
            $this->selectedVariantId = $default->id;
            foreach ($default->optionValues as $value) {
                $this->selectedOptions[$value->product_option_id] = $value->id;
            }
        }

        $this->quantity = max(1, $this->product->min_order_quantity);
    }

    public function updatedSelectedOptions(): void
    {
        $valueIds = collect($this->selectedOptions)->filter()->map(fn ($id) => (int) $id)->sort()->values();
        $this->selectedVariantId = $valueIds->count() === $this->product->options->count()
            ? $this->product->activeVariants->firstWhere('combination_key', $valueIds->implode('-'))?->id
            : null;
    }

    public function addToCart(CartService $cart): mixed
    {
        $this->validate([
            'selectedVariantId' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:'.$this->product->min_order_quantity],
        ], ['selectedVariantId.required' => 'Pilih seluruh opsi produk terlebih dahulu.']);

        if (! Auth::check()) {
            return redirect()->route('login');
        }

        abort_unless($this->product->activeVariants->contains('id', $this->selectedVariantId), 422);
        $cart->add(Auth::user(), $this->selectedVariantId, $this->quantity);
        $this->dispatch('cartUpdated');
        $this->dispatch('toast', variant: 'success', message: 'Produk ditambahkan ke keranjang.');

        return null;
    }

    public function render()
    {
        $selectedVariant = $this->product->activeVariants->firstWhere('id', $this->selectedVariantId);
        $selectedVariant?->setRelation('product', $this->product);

        return view('livewire.product-detail', [
            'selectedVariant' => $selectedVariant,
        ]);
    }
}
