<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $product ? 'Manage Product' : 'Create Product' }}
            </h1>

            <p class="text-sm text-gray-500">
                {{ $product ? 'Update product information.' : 'Create a new product.' }}
            </p>
        </div>

        <a href="{{ route('admin.products') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm hover:bg-gray-100">
            Back
        </a>
    </div>

    @if(session()->has('success'))
        <div class="rounded-lg bg-green-100 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="mb-2 block font-medium text-gray-700">
                    Product Name
                </label>
                <input type="text" wire:model.live="name" class="w-full rounded-lg border border-gray-300 p-2 focus:border-blue-500 focus:outline-none">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block font-medium text-gray-700">
                    SKU
                </label>
                <input type="text" wire:model="sku" class="w-full rounded-lg border border-gray-300 p-2 focus:border-blue-500 focus:outline-none">
                @error('sku')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block font-medium text-gray-700">
                    Slug
                </label>
                <input type="text" wire:model="slug" class="w-full rounded-lg border border-gray-300 p-2 focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="mb-2 block font-medium text-gray-700">
                    Price
                </label>
                <input type="number" wire:model="price" class="w-full rounded-lg border border-gray-300 p-2 focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="mb-2 block font-medium text-gray-700">
                    Stock Quantity
                </label>
                <input type="number" wire:model="stock_quantity" class="w-full rounded-lg border border-gray-300 p-2 focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="mb-2 block font-medium text-gray-700">
                    Stock Status
                </label>
                <select wire:model="stock_status" class="w-full rounded-lg border border-gray-300 p-2 focus:border-blue-500 focus:outline-none bg-white">
                    <option value="available">Available</option>
                    <option value="preorder">Pre Order</option>
                    <option value="out_of_stock">Out of Stock</option>
                </select>
            </div>

            <div>
                <label class="mb-2 block font-medium text-gray-700">
                    Weight (gram)
                </label>
                <input type="number" wire:model="weight_grams" class="w-full rounded-lg border border-gray-300 p-2 focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="mb-2 block font-medium text-gray-700">
                    Pre Order Days
                </label>
                <input type="number" wire:model="preorder_days" class="w-full rounded-lg border border-gray-300 p-2 focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="mb-2 block font-medium text-gray-700">
                    Minimum Order
                </label>
                <input type="number" wire:model="min_order_quantity" class="w-full rounded-lg border border-gray-300 p-2 focus:border-blue-500 focus:outline-none">
            </div>

            <div class="flex items-center gap-3 mt-8">
                <input type="checkbox" wire:model="is_active" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label class="font-medium text-gray-700">
                    Active Product
                </label>
            </div>
        </div>

        <div class="mt-6">
            <label class="mb-2 block font-medium text-gray-700">
                Description
            </label>
            <textarea rows="6" wire:model="description" class="w-full rounded-lg border border-gray-300 p-2 focus:border-blue-500 focus:outline-none"></textarea>
        </div>

        <div class="mt-6">
            <label class="mb-2 block font-medium text-gray-700">
                Gallery
            </label>

            <div class="rounded-lg border-2 border-dashed border-gray-300 p-8 text-center text-gray-400">
                <p class="mb-2 font-medium">Upload Gallery</p>

                <input type="file" wire:model="gallery" multiple accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer"/>
                
                @error('gallery.*')
                    <p class="mt-2 text-sm text-red-500">
                        {{ $message }}
                    </p>
                @enderror

                @if($gallery)
                    <div class="mt-6 grid grid-cols-4 gap-4">
                        @foreach($gallery as $image)
                            <img src="{{ $image->temporaryUrl() }}" class="h-32 w-full rounded-lg border object-cover">
                        @endforeach
                    </div>
                @endif

                @if($product && $product->images->count())
                    <div class="mt-6">
                        <h3 class="mb-3 font-semibold text-gray-700 text-left">
                            Current Gallery
                        </h3>
                        <div class="grid grid-cols-4 gap-4">
                            @foreach($product->images as $image)
                                <img src="{{ Storage::url($image->image_path) }}" class="h-32 w-full rounded-lg border object-cover">
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            @if($product)
                <button wire:click="delete" wire:confirm="Delete this product?" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm transition duration-150">
                    Delete
                </button>
            @endif

            <button wire:click="save" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg text-sm transition duration-150">
                Save
            </button>
        </div>
    </div>
</div>