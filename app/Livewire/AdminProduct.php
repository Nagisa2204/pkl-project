<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

class AdminProduct extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory_id = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory_id()
    {
        $this->resetPage();
    }
    
    public function deleteProduct($id)
    {
        Product::findorFail($id)->delete();
        session()->flash('success', 'Product deleted successfully.');
    }

    public function toogleStatus($id)
    {
        $product = Product::findorFail($id);
        $product->update([
            'is_active' => !$product->is_active
        ]);
    }

    public function render()
    {
        $products = Product::with('category', 'images');

        if ($this->search) {
            $products->where('name', 'like', "%{$this->search}%");
        }

        if ($this->selectedCategory) {
            $products->where('category_id', $this->selectedCategory);
        }

        return view('livewire.admin-product', [
            'products' => $products->latest()->paginate(10),
            'categories' => Category::all(),
        ]);
    }
}
