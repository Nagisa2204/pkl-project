<div class="ui-page max-w-6xl">
    <h1 class="mb-6 text-2xl font-extrabold text-content">Keranjang Belanja</h1>

    @if($cart && $cart->cartItems->isNotEmpty())
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
            <div class="space-y-3">
                @foreach($cart->cartItems as $item)
                    @php($product = $item->variant->product)
                    <x-ui.card class="flex flex-col gap-4 sm:flex-row sm:items-center" wire:key="cart-item-{{ $item->id }}">
                        <input wire:model.live="selectedItems" type="checkbox" value="{{ $item->id }}" class="rounded border-default text-primary">

                        @if($product->images->first())
                            <img src="{{ Storage::url($product->images->first()->image_path) }}" alt="{{ $product->name }}" class="h-20 w-20 rounded-ui object-cover">
                        @else
                            <div class="flex h-20 w-20 items-center justify-center rounded-ui bg-subtle text-xs text-muted">Tanpa gambar</div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-content">{{ $product->name }}</div>
                            <div class="text-sm text-muted">{{ $item->variant->displayName() }} · {{ $item->variant->sku }}</div>
                            <div class="font-semibold text-primary">Rp {{ number_format($item->variant->price, 0, ',', '.') }}</div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                            <div class="flex items-center gap-2" aria-label="Jumlah produk">
                                <x-ui.button wire:click="decrementQuantity({{ $item->id }})" variant="outline" size="sm" aria-label="Kurangi jumlah">−</x-ui.button>
                                <span class="w-8 text-center font-semibold">{{ $item->quantity }}</span>
                                <x-ui.button wire:click="incrementQuantity({{ $item->id }})" variant="outline" size="sm" aria-label="Tambah jumlah">+</x-ui.button>
                            </div>

                            <x-ui.confirm-action
                                action="removeItem({{ $item->id }})"
                                title="Hapus produk"
                                message="{{ $product->name }} akan dihapus dari keranjang."
                                confirm-label="Hapus"
                                button-variant="ghost"
                                size="sm"
                                class="text-danger"
                            >Hapus</x-ui.confirm-action>
                        </div>
                    </x-ui.card>
                @endforeach
                <x-input-error :messages="$errors->get('cart')" />
            </div>

            <aside class="ui-card h-fit p-5 lg:sticky lg:top-5">
                <h2 class="font-bold text-content">Ringkasan</h2>
                <div class="mt-4 flex justify-between text-sm"><span class="text-muted">Total item</span><strong>{{ $totalItems }}</strong></div>
                <div class="mt-2 flex justify-between text-lg"><span>Total</span><strong class="text-primary">Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></div>
                <x-ui.button :href="route('checkout')" size="lg" class="mt-5 w-full {{ empty($selectedItems) ? 'pointer-events-none opacity-50' : '' }}">Checkout</x-ui.button>
                @if(empty($selectedItems))<p class="mt-2 text-center text-xs text-muted">Pilih minimal satu produk.</p>@endif
            </aside>
        </div>
    @else
        <div class="ui-empty-state">
            <p class="font-semibold text-content">Keranjang masih kosong</p>
            <p class="mt-1 text-sm">Tambahkan produk untuk mulai berbelanja.</p>
            <x-ui.button :href="route('product.index')" class="mt-4">Lihat produk</x-ui.button>
        </div>
    @endif
</div>
