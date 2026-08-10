@props([
    'product',
    'showAction' => true,
    'showCategory' => true,
])

@php
    $variant = $product->defaultVariant ?? $product->activeVariants->first();
    $variant?->setRelation('product', $product);
    $image = $product->images->first();
    $hasChoices = $product->activeVariants->count() > 1;
    $isPurchasable = $variant?->isPurchasable() ?? false;
    $isPreorder = $variant?->stock_status === \App\Enums\StockStatus::Preorder;
    $compareAtPrice = (int) ($variant?->compare_at_price ?? 0);
    $price = (int) ($variant?->price ?? 0);
    $soldQuantity = (int) ($product->sold_quantity ?? 0);
    $detailUrl = route('product.detail', $product->slug);
@endphp

<article {{ $attributes->class('ui-product-card') }}>
    <a href="{{ $detailUrl }}" class="relative block overflow-hidden bg-subtle" wire:navigate>
        @if($image)
            <img src="{{ Storage::url($image->image_path) }}" alt="{{ $image->alt_text ?: $product->name }}" class="h-52 w-full object-cover transition duration-300 hover:scale-[1.02]" loading="lazy">
        @else
            <div class="flex h-52 items-center justify-center text-sm text-muted">Belum ada gambar</div>
        @endif

        <div class="absolute left-3 top-3 flex flex-wrap gap-2">
            @if($isPreorder)
                <x-ui.badge variant="warning">Preorder</x-ui.badge>
            @elseif(!$isPurchasable)
                <x-ui.badge variant="danger">Stok habis</x-ui.badge>
            @elseif($compareAtPrice > $price)
                <x-ui.badge variant="success">Diskon {{ (int) round((1 - ($price / $compareAtPrice)) * 100) }}%</x-ui.badge>
            @endif
        </div>
    </a>

    <div class="flex flex-1 flex-col p-4">
        @if($showCategory)
            <p class="text-xs font-semibold text-primary">{{ $product->category?->name }}</p>
        @endif
        <a href="{{ $detailUrl }}" class="mt-1 line-clamp-2 font-bold leading-5 text-content hover:text-primary" wire:navigate>{{ $product->name }}</a>

        <div class="mt-3">
            <p class="text-lg font-extrabold text-primary">Rp {{ number_format($price, 0, ',', '.') }}</p>
            @if($compareAtPrice > $price)
                <p class="text-sm text-muted line-through">Rp {{ number_format($compareAtPrice, 0, ',', '.') }}</p>
            @endif
        </div>

        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted">
            <span>{{ $hasChoices ? $product->activeVariants->count().' varian' : ($isPreorder ? 'Preorder' : 'Stok '.($variant?->stock_quantity ?? 0)) }}</span>
            <span aria-hidden="true">•</span>
            <span>{{ number_format($soldQuantity, 0, ',', '.') }} terjual</span>
        </div>

        @if($showAction)
            <div class="mt-auto pt-4">
                @if($hasChoices)
                    <x-ui.button :href="$detailUrl" variant="secondary" class="w-full" wire:navigate>Pilih varian</x-ui.button>
                @else
                    <x-ui.button
                        wire:click="addToCart({{ $variant?->id ?? 0 }})"
                        wire:loading.attr="disabled"
                        wire:target="addToCart({{ $variant?->id ?? 0 }})"
                        :variant="$isPurchasable ? 'secondary' : 'outline'"
                        :disabled="!$isPurchasable"
                        class="w-full"
                    >
                        {{ $isPurchasable ? 'Tambah ke keranjang' : 'Stok tidak tersedia' }}
                    </x-ui.button>
                @endif
            </div>
        @endif
    </div>
</article>
