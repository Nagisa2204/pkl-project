<div>
    <section class="bg-dark px-4 py-16 text-primary-foreground sm:py-20">
        <div class="mx-auto max-w-6xl">
            <p class="text-sm font-bold uppercase tracking-widest text-info">Selamat datang</p>
            <h1 class="mt-3 max-w-3xl text-4xl font-extrabold md:text-6xl">{{ $storeSettings->store_name }}</h1>
            <p class="mt-5 max-w-2xl text-lg text-light/80">{{ $storeSettings->description ?: 'Temukan produk terbaik dan selesaikan pembelian dengan pengiriman serta pembayaran yang aman.' }}</p>
            <x-ui.button :href="route('product.index')" size="lg" variant="outline" class="mt-7" wire:navigate>Belanja sekarang</x-ui.button>
        </div>
    </section>

    <section class="mx-auto grid max-w-6xl gap-4 px-4 py-10 md:grid-cols-3">
        <x-ui.card>
            <strong class="text-content">Pembayaran aman</strong>
            <p class="mt-1 text-sm text-muted">Metode dipilih di Snap dan status diverifikasi langsung melalui webhook Midtrans.</p>
        </x-ui.card>
        <x-ui.card>
            <strong class="text-content">Ongkir transparan</strong>
            <p class="mt-1 text-sm text-muted">Tarif pengiriman dihitung sesuai alamat dan berat produk.</p>
        </x-ui.card>
        <x-ui.card>
            <strong class="text-content">Bantuan toko</strong>
            <p class="mt-1 text-sm text-muted">{{ $storeSettings->whatsapp ?: $storeSettings->email ?: 'Hubungi kami melalui halaman informasi toko.' }}</p>
        </x-ui.card>
    </section>

    @if($featuredProducts->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 pb-12">
            <div class="mb-5 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-content">Produk terbaru</h2>
                    <p class="mt-1 text-sm text-muted">Pilihan terbaru dari katalog kami.</p>
                </div>
                <a href="{{ route('product.index') }}" class="text-sm font-semibold text-primary hover:text-primary-hover" wire:navigate>Lihat semua</a>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($featuredProducts as $product)
                    <x-product-card :product="$product" wire:key="featured-product-{{ $product->id }}" />
                @endforeach
            </div>
        </section>
    @endif
</div>
