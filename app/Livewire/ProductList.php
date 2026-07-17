<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;


class ProductList extends Component
{
    public function render()    
    {
        return view('livewire.product-list');
    }

    public function index()
    {
        if (Auth::check()) {
            $products = Product::with('category', 'images') ->paginate(10);
            
            return view('livewire.product-list', compact('products'));
        } else {
            return view('livewire.product-list');
        }
    }

    public function show($id)
    {
        $product = Product::with('category', 'images', 'price') ->find($id);
        return view('livewire.product-show', compact('product'));
    }
}