<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $selectedCategory = null;
    public string $sort = 'latest';
    public ?int $minPrice = null;
    public ?int $maxPrice = null;

    public function mount(?string $category = null): void
    {
        if ($category) {
            $this->selectedCategory = Category::where('slug', $category)->value('id');
        }
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'selectedCategory', 'sort', 'minPrice', 'maxPrice'], true)) {
            $this->resetPage();
        }
    }

    public function addToCart(int $variantId, CartService $cart): mixed
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $cart->add(Auth::user(), $variantId);
        $this->dispatch('cartUpdated');
        $this->dispatch('alert', type: 'success', message: 'Produk ditambahkan ke keranjang.');

        return null;
    }

    public function render()
    {
        $products = Product::query()
            ->active()
            ->with(['images', 'category', 'defaultVariant.optionValues', 'activeVariants'])
            ->whereHas('activeVariants')
            ->when($this->search, fn ($query) => $query->where(function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhereHas('activeVariants', fn ($variants) => $variants->where('sku', 'like', '%'.$this->search.'%'));
            }))
            ->when($this->selectedCategory, fn ($query) => $query->where('category_id', $this->selectedCategory))
            ->when($this->minPrice !== null, fn ($query) => $query->whereHas('activeVariants', fn ($variants) => $variants->where('price', '>=', $this->minPrice)))
            ->when($this->maxPrice !== null, fn ($query) => $query->whereHas('activeVariants', fn ($variants) => $variants->where('price', '<=', $this->maxPrice)));

        match ($this->sort) {
            'price_low' => $products->withMin('activeVariants', 'price')->orderBy('active_variants_min_price'),
            'price_high' => $products->withMax('activeVariants', 'price')->orderByDesc('active_variants_max_price'),
            'name' => $products->orderBy('name'),
            default => $products->latest(),
        };

        return view('livewire.product-list', [
            'products' => $products->paginate(12),
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
