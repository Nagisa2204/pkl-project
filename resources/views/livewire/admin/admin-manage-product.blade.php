<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $product ? 'Manage Product' : 'Create Product' }}
            </h1>

            <p class="text-sm text-gray-500">
                {{ $product ? 'Update product information.' : 'Create a new product.' }}
            </p>
        </div>

        <a
            href="{{ route('admin.products') }}"
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm hover:bg-gray-100"
        >
            Back
        </a>

    </div>

    {{-- Success Message --}}
    @if(session()->has('success'))

        <div class="rounded-lg bg-green-100 p-4 text-green-700">
            {{ session('success') }}
        </div>

    @endif

    {{-- Form --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

        <div class="grid grid-cols-2 gap-6">

            {{-- Name --}}
            <div>

                <label class="mb-2 block font-medium">
                    Product Name
                </label>

                <input
                    type="text"
                    wire:model.live="name"
                    class="w-full rounded-lg border border-gray-300"
                >

                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror

            </div>

            {{-- SKU --}}
            <div>

                <label class="mb-2 block font-medium">
                    SKU
                </label>

                <input
                    type="text"
                    wire:model="sku"
                    class="w-full rounded-lg border border-gray-300"
                >

                @error('sku')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror

            </div>

            {{-- Slug --}}
            <div>

                <label class="mb-2 block font-medium">
                    Slug
                </label>

                <input
                    type="text"
                    wire:model="slug"
                    class="w-full rounded-lg border border-gray-300"
                >

            </div>

            {{-- Price --}}
            <div>

                <label class="mb-2 block font-medium">
                    Price
                </label>

                <input
                    type="number"
                    wire:model="price"
                    class="w-full rounded-lg border border-gray-300"
                >

            </div>

            {{-- Stock --}}
            <div>

                <label class="mb-2 block font-medium">
                    Stock Quantity
                </label>

                <input
                    type="number"
                    wire:model="stock_quantity"
                    class="w-full rounded-lg border border-gray-300"
                >

            </div>

            {{-- Stock Status --}}
            <div>

                <label class="mb-2 block font-medium">
                    Stock Status
                </label>

                <select
                    wire:model="stock_status"
                    class="w-full rounded-lg border border-gray-300"
                >

                    <option value="available">
                        Available
                    </option>

                    <option value="preorder">
                        Pre Order
                    </option>

                    <option value="out_of_stock">
                        Out of Stock
                    </option>

                </select>

            </div>

            {{-- Weight --}}
            <div>

                <label class="mb-2 block font-medium">
                    Weight (gram)
                </label>

                <input
                    type="number"
                    wire:model="weight_grams"
                    class="w-full rounded-lg border border-gray-300"
                >

            </div>

            {{-- Pre Order --}}
            <div>

                <label class="mb-2 block font-medium">
                    Pre Order Days
                </label>

                <input
                    type="number"
                    wire:model="preorder_days"
                    class="w-full rounded-lg border border-gray-300"
                >

            </div>

            {{-- Minimum Order --}}
            <div>

                <label class="mb-2 block font-medium">
                    Minimum Order
                </label>

                <input
                    type="number"
                    wire:model="min_order_quantity"
                    class="w-full rounded-lg border border-gray-300"
                >

            </div>

            {{-- Active --}}
            <div class="flex items-center gap-3 mt-8">

                <input
                    type="checkbox"
                    wire:model="is_active"
                    class="rounded border-gray-300"
                >

                <label>
                    Active Product
                </label>

            </div>

        </div>

        {{-- Description --}}
        <div class="mt-6">

            <label class="mb-2 block font-medium">
                Description
            </label>

            <textarea
                rows="6"
                wire:model="description"
                class="w-full rounded-lg border border-gray-300"
            ></textarea>

        </div>

        {{-- Gallery --}}
        <div class="mt-6">

            <label class="mb-2 block font-medium">
                Gallery
            </label>

            <div class="rounded-lg border-2 border-dashed border-gray-300 p-8 text-center text-gray-400">

                Upload Gallery
                <br>

                <input type="file" wire:model="gallery" multiple accept="image/*" class="block w-full"/>
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
                        <h3 class="mb-3 font-semibold">
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

        {{-- Button --}}
        <div class="mt-8 flex justify-end gap-3">

            @if($product)

                <button
                    wire:click="delete"
                    wire:confirm="Delete this product?"
                    class="rounded-lg bg-red-600 px-6 py-2 text-white hover:bg-red-700"
                >
                    Delete
                </button>

            @endif

            <button
                wire:click="save"
                class="rounded-lg bg-green-600 px-6 py-2 text-white hover:bg-green-700"
            >
                Save Product
            </button>

        </div>

    </div>

</div>