<div class="ui-page max-w-6xl">
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-content">Checkout</h1>
        <p class="mt-1 text-sm text-muted">Lengkapi alamat dan pengiriman. Metode pembayaran dipilih langsung melalui
            Midtrans.</p>
    </div>

    <form wire:submit="placeOrder" class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">
        <div class="space-y-5">
            <x-ui.card>
                <h2 class="mb-4 text-lg font-bold text-content">Kontak penerima</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="ui-field-label" for="buyer_name">Nama penerima</label>
                        <input id="buyer_name" wire:model="buyer_name" class="ui-field">
                        <x-input-error :messages="$errors->get('buyer_name')" />
                    </div>
                    <div>
                        <label class="ui-field-label" for="buyer_whatsapp">WhatsApp</label>
                        <input id="buyer_whatsapp" wire:model="buyer_whatsapp" class="ui-field">
                        <x-input-error :messages="$errors->get('buyer_whatsapp')" />
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-lg font-bold text-content">Alamat pengiriman</h2>
                    <a href="{{ route('profile.show') }}"
                        class="text-sm font-semibold text-primary hover:text-primary-hover">Kelola alamat</a>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse($addresses as $address)
                        <label
                            class="flex cursor-pointer gap-3 rounded-ui border p-4 transition {{ $shipping_address_id === $address->id ? 'border-primary bg-info-soft' : 'border-default hover:border-primary/40' }}">
                            <input type="radio" wire:model.live="shipping_address_id" value="{{ $address->id }}">
                            <span>
                                <strong class="text-content">{{ $address->label }} ·
                                    {{ $address->receiver_name }}</strong>
                                <span class="mt-1 block text-sm text-muted">{{ $address->address_line }},
                                    {{ $address->destination_label }} {{ $address->postal_code }}</span>
                            </span>
                        </label>
                    @empty
                        <x-ui.alert variant="warning">Alamat belum tersedia. Isi alamat pada halaman profil sebelum
                            checkout.</x-ui.alert>
                    @endforelse
                </div>
                <x-input-error :messages="$errors->get('shipping_address_id')" />
            </x-ui.card>

            <x-ui.card>
                <h2 class="mb-4 text-lg font-bold text-content">Pengiriman</h2>

                <x-ui.searchable-select wire:model.live="courier" :options="collect($couriers)->mapWithKeys(fn($code) => [$code => strtoupper($code)])->all()" label="Kurir"
                    placeholder="Pilih kurir" search-placeholder="Cari kurir..." loading-target="courier" />
                <x-input-error :messages="$errors->get('courier')" />

                <div class="mt-4 space-y-2">
                    <div wire:loading.flex wire:target="courier" class="items-center gap-2 text-sm text-primary">
                        <span
                            class="h-4 w-4 animate-spin rounded-full border-2 border-primary border-r-transparent"></span>
                        Menghitung ongkir...
                    </div>

                    @forelse($services as $service)
                        @php($serviceCode = (string) ($service['service'] ?? ''))
                        <label
                            class="flex cursor-pointer flex-col justify-between gap-2 rounded-ui border p-3 transition sm:flex-row sm:items-center {{ $shipping_service === $serviceCode ? 'border-primary bg-info-soft' : 'border-default hover:border-primary/40' }}">
                            <span class="flex items-start gap-2">
                                <input type="radio" wire:model.live="shipping_service" value="{{ $serviceCode }}">
                                <span>
                                    <strong class="text-content">{{ $serviceCode }}</strong>
                                    <small class="block text-muted">{{ $service['description'] ?? '' }} · Estimasi
                                        {{ $service['etd'] ?? '-' }}</small>
                                </span>
                            </span>
                            <strong class="text-content">Rp
                                {{ number_format((int) ($service['cost'] ?? 0), 0, ',', '.') }}</strong>
                        </label>
                    @empty
                        @if ($courier && !$errors->has('courier'))
                            <p class="text-sm text-muted">Tidak ada layanan tersedia untuk kurir ini.</p>
                        @endif
                    @endforelse
                </div>
                <x-input-error :messages="$errors->get('shipping_service')" />
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-start gap-3">
                    <span
                        class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-info-soft font-bold text-primary">M</span>
                    <div>
                        <h2 class="font-bold text-content">Pembayaran melalui Midtrans Snap</h2>
                        <p class="mt-1 text-sm leading-6 text-muted">Setelah pesanan dibuat, jendela pembayaran Midtrans
                            akan terbuka.</p>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <aside class="ui-card h-fit p-5 lg:sticky lg:top-5">
            <h2 class="font-bold text-content">Ringkasan pesanan</h2>
            @php($subtotal = 0)
            <div class="mt-4 space-y-3">
                @foreach ($cart?->cartItems ?? [] as $item)
                    @php($line = $item->variant->price * $item->quantity)
                    @php($subtotal += $line)
                    <div class="flex justify-between gap-3 text-sm">
                        <span class="min-w-0 text-content">{{ $item->variant->product->name }}<small
                                class="block text-muted">{{ $item->variant->displayName() }} ·
                                {{ $item->quantity }}x</small></span>
                        <strong class="shrink-0">Rp {{ number_format($line, 0, ',', '.') }}</strong>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 border-t border-default pt-4 text-sm">
                <div class="flex justify-between"><span class="text-muted">Subtotal</span><strong>Rp
                        {{ number_format($subtotal, 0, ',', '.') }}</strong></div>
                <div class="mt-2 flex justify-between"><span
                        class="text-muted">Ongkir</span><strong>{{ $shipping_cost === null ? 'Belum dipilih' : 'Rp ' . number_format($shipping_cost, 0, ',', '.') }}</strong>
                </div>
            </div>
            <div class="mt-4 flex justify-between border-t border-default pt-4 text-lg"><strong>Total</strong><strong
                    class="text-primary">Rp
                    {{ number_format($subtotal + ($shipping_cost ?? 0), 0, ',', '.') }}</strong></div>

            <x-input-error :messages="$errors->get('cart')" class="mt-3" />
            <x-input-error :messages="$errors->get('checkout')" class="mt-3" />

            <x-ui.button type="submit" size="lg" wire:loading.attr="disabled" wire:target="placeOrder"
                :disabled="!$has_address || $shipping_cost === null || !$shipping_service || $orderCreated" class="mt-5 w-full">
                <span wire:loading.remove wire:target="placeOrder">Buat pesanan & bayar</span>
                <span wire:loading wire:target="placeOrder">Menyiapkan pembayaran...</span>
            </x-ui.button>
            <p class="mt-3 text-xs leading-5 text-muted">Status pembayaran hanya diperbarui oleh webhook Midtrans yang
                telah diverifikasi backend.</p>
        </aside>
    </form>

    <x-midtrans-snap />
</div>
