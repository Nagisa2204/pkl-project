<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Layout('layouts.admin')]
class AdminManageProduct extends Component
{
    use WithFileUploads;
    public ?Product $product = null;

    public $name = '';
    public $slug = '';
    public $sku = '';
    public $description = '';

    public $price = 0;

    public $stock_status = 'available';
    public $stock_quantity = 0;

    public $weight_grams = 0;
    public $preorder_days = 0;
    public $min_order_quantity = 1;

    public $gallery = [];

    public $is_active = true;

    protected function rules()
    {
        $productId = $this->product?->id;

        return [
            'name' => 'required|string|max:255',

            'slug' => 'required|string|max:255|unique:products,slug,' . $productId,

            'sku' => 'required|string|max:255|unique:products,sku,' . $productId,

            'description' => 'nullable|string',

            'price' => 'required|numeric|min:0',

            'stock_status' => 'required|string',

            'stock_quantity' => 'required|integer|min:0',

            'weight_grams' => 'required|integer|min:0',

            'preorder_days' => 'required|integer|min:0',

            'min_order_quantity' => 'required|integer|min:1',

            'gallery' => 'nullable|array',

            'gallery.*' => 'nullable|image|max:2048',

            'is_active' => 'boolean',
        ];
    }

    public function mount(Product $product = null)
    {
        if ($product && $product->exists) {

            $this->product = $product;

            $this->name = $product->name;
            $this->slug = $product->slug;
            $this->sku = $product->sku;
            $this->description = $product->description;

            $this->price = $product->price;

            $this->stock_status = $product->stock_status;
            $this->stock_quantity = $product->stock_quantity;

            $this->weight_grams = $product->weight_grams;
            $this->preorder_days = $product->preorder_days;
            $this->min_order_quantity = $product->min_order_quantity;

            $this->is_active = $product->is_active;
        }
    }

    public function updatedName()
    {
        if (!$this->product) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function save()
    {
        $this->validate();

        $category = Category::first();

        if (!$category) {
            session()->flash(
                'error',
                'Kategori Produk Fisik belum tersedia.'
            );

            return;
        }

        $data = [

            'category_id' => $category->id,

            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,

            'price' => $this->price,

            'stock_status' => $this->stock_status,
            'stock_quantity' => $this->stock_quantity,

            'weight_grams' => $this->weight_grams,
            'preorder_days' => $this->preorder_days,
            'min_order_quantity' => $this->min_order_quantity,

            'is_active' => $this->is_active
        ];
        
        if ($this->product) {
            $this->product->update($data);
            $product = $this->product;
        } else {
            $product = Product::create($data);
        }
        $firstImagePath = $product->thumbnail;
        $nextSortOrder = $product->images()->max('sort_order') ?? -1;

        foreach ($this->gallery as $image) {

            $nextSortOrder++;
            $path = $image->store('products/gallery', 'public');
            if (empty($firstImagePath)) {
                $firstImagePath = $path;
            }

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'caption' => null,
                'sort_order' => $nextSortOrder,
            ]);
        }

        if ($product->thumbnail !== $firstImagePath) {
            $product->update([
                'thumbnail' => $firstImagePath,
            ]);
        }

        session()->flash(
            'success',
            'Product saved successfully.'
        );

        return redirect()->route('admin.products');
    }

    public function delete()
    {
        if (!$this->product) {
            return;
        }

        if ($this->product->thumbnail && Storage::disk('public')->exists($this->product->thumbnail)) {
            Storage::disk('public')->delete($this->product->thumbnail);
        }

        $this->product->delete($this->product->id);

        session()->flash(
            'success',
            'Product deleted successfully.'
        );

        return redirect()->route('admin.products');
    }

    public function render()
    {
        return view('livewire.admin.admin-manage-product');
    }
}