<div class="mx-auto max-w-6xl px-4 py-8">
    <h1 class="mb-6 text-2xl font-extrabold text-slate-900">Keranjang Belanja</h1>
    @if($cart && $cart->cartItems->isNotEmpty())
        <div class="grid gap-6 lg:grid-cols-[1fr_340px]">
            <div class="space-y-3">
                @foreach($cart->cartItems as $item)
                    @php($product = $item->variant->product)
                    <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm">
                        <input wire:model.live="selectedItems" type="checkbox" value="{{ $item->id }}" class="rounded border-slate-300 text-indigo-600">
                        @if($product->images->first())
                            <img src="{{ Storage::url($product->images->first()->image_path) }}" alt="{{ $product->name }}" class="h-20 w-20 rounded-lg object-cover">
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-slate-900">{{ $product->name }}</div>
                            <div class="text-sm text-slate-500">{{ $item->variant->displayName() }} · {{ $item->variant->sku }}</div>
                            <div class="font-semibold text-indigo-600">Rp {{ number_format($item->variant->price, 0, ',', '.') }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="decrementQuantity({{ $item->id }})" class="h-9 w-9 rounded border">−</button>
                            <span class="w-8 text-center">{{ $item->quantity }}</span>
                            <button wire:click="incrementQuantity({{ $item->id }})" class="h-9 w-9 rounded border">+</button>
                        </div>
                        <button wire:click="removeItem({{ $item->id }})" wire:confirm="Hapus produk ini?" class="text-sm font-semibold text-red-600">Hapus</button>
                    </div>
                @endforeach
                <x-input-error :messages="$errors->get('cart')" />
            </div>
            <aside class="h-fit rounded-xl bg-white p-5 shadow-sm">
                <h2 class="font-bold text-slate-900">Ringkasan</h2>
                <div class="mt-4 flex justify-between text-sm"><span>Total item</span><strong>{{ $totalItems }}</strong></div>
                <div class="mt-2 flex justify-between text-lg"><span>Total</span><strong class="text-indigo-600">Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></div>
                <a href="{{ route('checkout') }}" class="mt-5 block rounded-lg bg-slate-900 px-4 py-3 text-center font-bold text-white {{ empty($selectedItems) ? 'pointer-events-none opacity-50' : '' }}">Checkout</a>
                @if(empty($selectedItems))<p class="mt-2 text-center text-xs text-slate-500">Pilih minimal satu produk.</p>@endif
            </aside>
        </div>
    @else
        <div class="rounded-xl border-2 border-dashed border-slate-200 py-16 text-center">
            <p class="text-slate-500">Keranjang masih kosong.</p>
            <a href="{{ route('product.index') }}" class="mt-4 inline-block rounded-lg bg-slate-900 px-5 py-2 text-white">Lihat produk</a>
        </div>
    @endif
</div>
