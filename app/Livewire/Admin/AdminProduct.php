<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

#[Layout('layouts.admin')]
class AdminProduct extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = '';

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->authorize('admin');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }
    
    public function deleteProduct($id)
    {
        $this->authorize('admin');
        Product::findOrFail($id)->delete();
        session()->flash('success', 'Product deleted successfully.');
    }

    public function toogleStatus($id)
    {
        $this->authorize('admin');
        $product = Product::findOrFail($id);
        $product->update([
            'is_active' => !$product->is_active
        ]);
    }

    public function render()
    {
        $products = Product::with(['category', 'images', 'defaultVariant', 'activeVariants']);

        if ($this->search) {
            $products->where('name', 'like', "%{$this->search}%");
        }

        if ($this->selectedCategory) {
            $products->where('category_id', $this->selectedCategory);
        }

        return view('livewire.admin.admin-product', [
            'products' => $products->latest()->paginate(10),
            'categories' => Category::all(),
        ]);
    }
}
