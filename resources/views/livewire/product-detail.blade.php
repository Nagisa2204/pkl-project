<div class="mx-auto max-w-6xl px-4 py-8">
    <div class="ui-card grid gap-8 p-6 md:grid-cols-2">
        <div>
            @php($primary = $product->images->first())
            @if($primary)
                <img src="{{ Storage::url($primary->image_path) }}" alt="{{ $primary->alt_text ?: $product->name }}" class="h-96 w-full rounded-xl object-cover">
            @else
                <div class="flex h-96 items-center justify-center rounded-xl bg-subtle text-muted">Belum ada gambar</div>
            @endif
            @if($product->images->count() > 1)
                <div class="mt-3 grid grid-cols-4 gap-2">
                    @foreach($product->images as $image)
                        <img src="{{ Storage::url($image->image_path) }}" alt="{{ $image->alt_text ?: $product->name }}" class="h-20 w-full rounded-lg object-cover">
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <div class="text-sm font-semibold text-primary">{{ $product->category->name }}</div>
            <h1 class="mt-1 text-3xl font-extrabold text-content">{{ $product->name }}</h1>
            <div class="mt-3 text-2xl font-extrabold text-primary">
                {{ $selectedVariant ? 'Rp '.number_format($selectedVariant->price, 0, ',', '.') : 'Pilih varian' }}
            </div>
            <div class="prose mt-5 max-w-none text-muted">{!! nl2br(e($product->description)) !!}</div>

            @foreach($product->options as $option)
                <fieldset class="mt-6">
                    <legend class="mb-2 text-sm font-bold text-content">{{ $option->name }}</legend>
                    <div class="flex flex-wrap gap-2">
                        @foreach($option->values as $value)
                            <label class="cursor-pointer">
                                <input class="peer sr-only" type="radio" wire:model.live="selectedOptions.{{ $option->id }}" value="{{ $value->id }}">
                                <span class="block rounded-lg border border-default px-4 py-2 text-sm peer-checked:border-primary peer-checked:bg-info-soft peer-checked:text-primary">{{ $value->value }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach

            @if($selectedVariant)
                <div class="mt-5 grid grid-cols-3 gap-3 rounded-xl bg-subtle p-4 text-sm">
                    <div><span class="block text-muted">SKU</span><strong>{{ $selectedVariant->sku }}</strong></div>
                    <div><span class="block text-muted">Stok</span><strong>{{ $selectedVariant->stock_status === \App\Enums\StockStatus::Preorder ? 'Preorder' : $selectedVariant->stock_quantity }}</strong></div>
                    <div><span class="block text-muted">Berat</span><strong>{{ number_format($selectedVariant->weight_grams) }} g</strong></div>
                </div>
            @endif

            <div class="mt-6 flex gap-3">
                <input wire:model="quantity" type="number" min="{{ $product->min_order_quantity }}" class="w-24 rounded-lg border-default">
                <button wire:click="addToCart" wire:loading.attr="disabled" @disabled(!$selectedVariant?->isPurchasable($quantity)) class="flex-1 rounded-lg px-5 py-3 font-bold text-primary-foreground {{ $selectedVariant?->isPurchasable($quantity) ? 'bg-dark' : 'cursor-not-allowed bg-muted' }}">Tambah ke keranjang</button>
            </div>
            <x-input-error :messages="$errors->get('selectedVariantId')" class="mt-2" />
            <x-input-error :messages="$errors->get('cart')" class="mt-2" />
        </div>
    </div>
</div>
