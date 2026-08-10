<div class="mx-auto max-w-6xl px-4 py-8">
    <h1 class="mb-6 text-2xl font-extrabold text-slate-900">Checkout</h1>
    <form wire:submit="placeOrder" class="grid gap-6 lg:grid-cols-[1fr_380px]">
        <div class="space-y-5">
            <section class="rounded-xl bg-white p-5 shadow-sm">
                <h2 class="mb-4 font-bold">Kontak</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <div><label class="text-sm">Nama penerima</label><input wire:model="buyer_name" class="mt-1 w-full rounded-lg border-slate-300"><x-input-error :messages="$errors->get('buyer_name')" /></div>
                    <div><label class="text-sm">WhatsApp</label><input wire:model="buyer_whatsapp" class="mt-1 w-full rounded-lg border-slate-300"><x-input-error :messages="$errors->get('buyer_whatsapp')" /></div>
                </div>
            </section>

            <section class="rounded-xl bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between"><h2 class="font-bold">Alamat pengiriman</h2><a href="{{ route('profile.show') }}" class="text-sm text-indigo-600">Kelola alamat</a></div>
                @forelse($addresses as $address)
                    <label class="mt-3 flex cursor-pointer gap-3 rounded-lg border p-4 {{ $shipping_address_id === $address->id ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200' }}">
                        <input type="radio" wire:model.live="shipping_address_id" value="{{ $address->id }}">
                        <span><strong>{{ $address->label }} · {{ $address->receiver_name }}</strong><span class="mt-1 block text-sm text-slate-600">{{ $address->address_line }}, {{ $address->destination_label }} {{ $address->postal_code }}</span></span>
                    </label>
                @empty
                    <p class="mt-3 rounded-lg bg-amber-50 p-4 text-sm text-amber-800">Alamat belum tersedia. Isi alamat pada halaman profil.</p>
                @endforelse
                <x-input-error :messages="$errors->get('shipping_address_id')" />
            </section>

            <section class="rounded-xl bg-white p-5 shadow-sm">
                <h2 class="mb-4 font-bold">Pengiriman</h2>
                <select wire:model.live="courier" class="w-full rounded-lg border-slate-300">
                    <option value="">Pilih kurir</option>
                    @foreach($couriers as $courierCode)<option value="{{ $courierCode }}">{{ strtoupper($courierCode) }}</option>@endforeach
                </select>
                <div wire:loading wire:target="courier" class="mt-2 text-sm text-indigo-600">Menghitung ongkir...</div>
                <x-input-error :messages="$errors->get('courier')" />
                <div class="mt-3 space-y-2">
                    @forelse($services as $service)
                        <label class="flex cursor-pointer justify-between rounded-lg border p-3 {{ $shipping_service === ($service['service'] ?? '') ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200' }}">
                            <span><input type="radio" wire:model.live="shipping_service" value="{{ $service['service'] }}"> <strong>{{ $service['service'] }}</strong> <small>{{ $service['description'] ?? '' }} · {{ $service['etd'] ?? '-' }}</small></span>
                            <strong>Rp {{ number_format((int) ($service['cost'] ?? 0), 0, ',', '.') }}</strong>
                        </label>
                    @empty
                        @if($courier)<p class="text-sm text-slate-500">Tidak ada layanan tersedia.</p>@endif
                    @endforelse
                </div>
                <x-input-error :messages="$errors->get('shipping_service')" />
            </section>

            <section class="rounded-xl bg-white p-5 shadow-sm">
                <h2 class="mb-4 font-bold">Pembayaran Midtrans</h2>
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach(['bca_va' => 'BCA VA', 'bni_va' => 'BNI VA', 'bri_va' => 'BRI VA', 'echannel' => 'Mandiri Bill', 'qris' => 'QRIS', 'all' => 'Semua metode'] as $code => $label)
                        <label class="rounded-lg border p-3 {{ $selected_bank === $code ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200' }}"><input type="radio" wire:model.live="selected_bank" value="{{ $code }}"> {{ $label }}</label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('selected_bank')" />
            </section>
        </div>

        <aside class="h-fit rounded-xl bg-white p-5 shadow-sm lg:sticky lg:top-5">
            <h2 class="font-bold">Ringkasan pesanan</h2>
            @php($subtotal = 0)
            <div class="mt-4 space-y-3">
                @foreach($cart?->cartItems ?? [] as $item)
                    @php($line = $item->variant->price * $item->quantity)
                    @php($subtotal += $line)
                    <div class="flex justify-between gap-3 text-sm"><span>{{ $item->variant->product->name }}<small class="block text-slate-500">{{ $item->variant->displayName() }} · {{ $item->quantity }}x</small></span><strong>Rp {{ number_format($line, 0, ',', '.') }}</strong></div>
                @endforeach
            </div>
            <div class="mt-4 border-t pt-4 text-sm"><div class="flex justify-between"><span>Subtotal</span><strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong></div><div class="mt-2 flex justify-between"><span>Ongkir</span><strong>{{ $shipping_cost === null ? 'Belum dipilih' : 'Rp '.number_format($shipping_cost, 0, ',', '.') }}</strong></div></div>
            <div class="mt-4 flex justify-between border-t pt-4 text-lg"><strong>Total</strong><strong class="text-indigo-600">Rp {{ number_format($subtotal + ($shipping_cost ?? 0), 0, ',', '.') }}</strong></div>
            <x-input-error :messages="$errors->get('cart')" class="mt-3" />
            <x-input-error :messages="$errors->get('checkout')" class="mt-3" />
            <button type="submit" wire:loading.attr="disabled" @disabled(!$has_address || $shipping_cost === null || !$shipping_service || !$selected_bank) class="mt-5 w-full rounded-lg bg-slate-900 px-4 py-3 font-bold text-white disabled:cursor-not-allowed disabled:opacity-50">Buat pesanan</button>
            <p class="mt-2 text-xs text-slate-500">Status pembayaran hanya diperbarui dari webhook Midtrans yang terverifikasi.</p>
        </aside>
    </form>
</div>
