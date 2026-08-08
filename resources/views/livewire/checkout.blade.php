<div class="max-w-[1100px] mx-auto py-10 px-5 font-sans">
    <h1 class="text-[28px] font-extrabold text-slate-900 mb-6">
        Checkout Pesanan
    </h1>

    <div class="flex flex-wrap gap-[30px] items-start">
        <div class="flex-[2] min-w-[60%]">
            <form wire:submit.prevent="placeOrder" id="checkout-form">
                <div class="bg-white p-[30px] rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] mb-5">
                    <h2 class="text-lg font-bold text-slate-800 border-b-2 border-slate-100 pb-4 mt-0 mb-5">
                        Informasi Kontak
                    </h2>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-2">
                                Nama Lengkap
                            </label>

                            <input type="text" wire:model="buyer_name" required class="w-full py-3 px-4 border border-slate-300 rounded-lg text-[15px] outline-none box-border focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">

                            @error('buyer_name')
                                <span class="text-red-500 text-xs">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-2">
                                Nomor WhatsApp
                            </label>

                            <input type="text" wire:model="buyer_whatsapp" required class="w-full py-3 px-4 border border-slate-300 rounded-lg text-[15px] outline-none box-border focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">

                            @error('buyer_whatsapp')
                                <span class="text-red-500 text-xs">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white p-[30px] rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] mb-5">
                    <h2 class="text-lg font-bold text-slate-800 border-b-2 border-slate-100 pb-4 mt-0 mb-5">
                        Alamat Pengiriman
                    </h2>

                    @if($has_address)
                        <div class="p-4 border border-indigo-600 bg-indigo-50 rounded-lg">
                            <span class="font-bold text-indigo-600 text-[15px]">
                                Alamat Utama Tersimpan
                            </span>

                            <p class="mt-1 mb-0 text-slate-600 text-sm">
                                Total Berat Pengiriman:
                                <strong>
                                    {{ number_format($total_weight, 0, ',', '.') }} Gram
                                </strong>
                            </p>
                        </div>
                    @else
                        <div class="py-6 px-5 border-2 border-dashed border-red-500 bg-red-50 rounded-lg text-center">
                            <svg class="mx-auto mb-2 text-red-500" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>

                            <h3 class="m-0 mb-2 text-red-800 text-lg font-bold">
                                Alamat Pengiriman Belum Diatur
                            </h3>

                            <p class="m-0 mb-5 text-red-700 text-sm">
                                Sistem membutuhkan alamat Anda untuk menghitung ongkos kirim.
                            </p>

                            <a href="{{ route('profile.show') }}" class="inline-block py-3 px-6 bg-red-500 hover:bg-red-600 text-white no-underline rounded-lg font-bold text-[15px] transition-colors duration-200">
                                Isi Alamat Sekarang
                            </a>
                        </div>
                    @endif
                </div>

                @if($has_address)
                    <div class="bg-white p-[30px] rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] mb-5">
                        <h2 class="text-lg font-bold text-slate-800 border-b-2 border-slate-100 pb-4 mt-0 mb-5">
                            Metode Pembayaran
                        </h2>
                        <div class="space-y-3">
                            <label class="flex items-center justify-between p-4 border rounded-lg cursor-pointer transition {{ $selected_bank === 'bca_va' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200 bg-white' }}">
                                <div class="flex items-center gap-3">
                                    <input type="radio" wire:model.live="selected_bank" value="bca_va" class="accent-indigo-600 w-4 h-4">
                                    <div>
                                        <div class="font-bold text-slate-800">
                                            BCA Virtual Account
                                        </div>

                                        <div class="text-xs text-slate-500">
                                            Pembayaran melalui BCA Virtual Account
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <label class="flex items-center justify-between p-4 border rounded-lg cursor-pointer transition {{ $selected_bank === 'bni_va' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200 bg-white' }}">
                                <div class="flex items-center gap-3">
                                    <input type="radio" wire:model.live="selected_bank" value="bni_va" class="accent-indigo-600 w-4 h-4">
                                    <div>
                                        <div class="font-bold text-slate-800">
                                            BNI Virtual Account
                                        </div>

                                        <div class="text-xs text-slate-500">
                                            Pembayaran melalui BNI Virtual Account
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <label class="flex items-center justify-between p-4 border rounded-lg cursor-pointer transition {{ $selected_bank === 'bri_va' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200 bg-white' }}">
                                <div class="flex items-center gap-3">
                                    <input type="radio" wire:model.live="selected_bank" value="bri_va" class="accent-indigo-600 w-4 h-4">
                                    <div>
                                        <div class="font-bold text-slate-800">
                                            BRI Virtual Account
                                        </div>

                                        <div class="text-xs text-slate-500">
                                            Pembayaran melalui BRI Virtual Account
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <label class="flex items-center justify-between p-4 border rounded-lg cursor-pointer transition {{ $selected_bank === 'echannel' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200 bg-white' }}">
                                <div class="flex items-center gap-3">
                                    <input type="radio" wire:model.live="selected_bank" value="echannel" class="accent-indigo-600 w-4 h-4">
                                    <div>
                                        <div class="font-bold text-slate-800">
                                            Mandiri Virtual Account
                                        </div>

                                        <div class="text-xs text-slate-500">
                                            Pembayaran melalui Mandiri Bill Payment
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <label class="flex items-center justify-between p-4 border rounded-lg cursor-pointer transition {{ $selected_bank === 'qris' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200 bg-white'}}">
                                <div class="flex items-center gap-3">
                                    <input type="radio" wire:model.live="selected_bank" value="qris" class="accent-indigo-600 w-4 h-4">
                                    <div>
                                        <div class="font-bold text-slate-800">
                                            QRIS
                                        </div>

                                        <div class="text-xs text-slate-500">
                                            Pembayaran melalui QRIS
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        
                        @error('selected_bank')
                            <span class="text-red-500 text-xs mt-2 block">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="bg-white p-[30px] rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)]">
                        <h2 class="text-lg font-bold text-slate-800 border-b-2 border-slate-100 pb-4 mt-0 mb-5">
                            Metode Pengiriman
                        </h2>

                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-slate-600 mb-2">
                                Pilih Kurir
                            </label>

                            <select wire:model.live="courier" required class="w-full py-3 px-4 border border-slate-300 rounded-lg text-[15px] outline-none box-border bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                <option value="">
                                    -- Pilih Ekspedisi --
                                </option>

                                <option value="jne">
                                    JNE
                                </option>

                                <option value="jnt">
                                    J&T Express
                                </option>

                                <option value="sicepat">
                                    SiCepat
                                </option>

                                <option value="ide">
                                    IDExpress
                                </option>

                                <option value="sap">
                                    SAP Express
                                </option>

                                <option value="ninja">
                                    Ninja
                                </option>

                                <option value="tiki">
                                    TIKI
                                </option>

                                <option value="pos">
                                    POS Indonesia
                                </option>

                                <option value="lion">
                                    Lion Parcel
                                </option>

                                <option value="wahana">
                                    Wahana Express
                                </option>

                            </select>

                            @error('courier')
                                <span class="text-red-500 text-xs">
                                    {{ $message }}
                                </span>
                            @enderror

                            <div wire:loading wire:target="courier" class="text-indigo-600 text-[13px] mt-2 font-semibold">
                                Memuat layanan dan harga dari RajaOngkir...
                            </div>

                        </div>
                        @if(count($services) > 0)
                            <div class="mt-4">
                                <label class="block text-sm font-semibold text-slate-600 mb-2">
                                    Pilih Layanan
                                </label>

                                @foreach($services as $svc)
                                    <label class="flex justify-between items-center p-4 border rounded-lg mb-2 cursor-pointer transition-colors duration-200 {{ $shipping_service === $svc['service'] ? 'bg-indigo-50 border-indigo-600' : 'bg-white border-slate-200'}}">
                                        <div class="flex items-center gap-3">

                                            <input type="radio" wire:model.live="shipping_service" value="{{ $svc['service'] }}" required class="accent-indigo-600 w-4 h-4">

                                            <div>
                                                <div class="font-bold text-slate-800 text-[15px]">
                                                    {{ $svc['service'] }}

                                                    @if(!empty($svc['description']))
                                                        <span class="text-xs text-slate-500 font-normal">
                                                            ({{ $svc['description'] }})
                                                        </span>
                                                    @endif
                                                </div>

                                                @if(!empty($svc['etd']))
                                                    <div class="text-[13px] text-slate-500 mt-0.5">
                                                        Estimasi Tiba: {{ $svc['etd'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="font-extrabold text-indigo-600 text-base">
                                            Rp {{ number_format((int) ($svc['cost'] ?? 0), 0, ',', '.') }}
                                        </div>
                                    </label>
                                @endforeach

                                @error('shipping_service')
                                    <span class="text-red-500 text-xs">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        @elseif($courier)
                            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="font-semibold text-yellow-800 text-sm">
                                    Layanan pengiriman tidak tersedia
                                </div>

                                <div class="text-yellow-700 text-xs mt-1">
                                    Tidak ada layanan dari courier ini untuk alamat dan berat paket yang dipilih.
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </form>
        </div>

        <div class="flex-1 min-w-[300px]">
            <div class="bg-white p-[25px] rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] sticky top-5">
                <h3 class="m-0 mb-5 text-lg font-extrabold text-slate-900 border-b-2 border-slate-100 pb-4">
                    Ringkasan Pesanan
                </h3>

                @php
                    $subtotalProduk = 0;
                @endphp

                <div class="mb-5">
                    @if($cart && $cart->cartItems)
                        @foreach($cart->cartItems as $item)
                            @php
                                $lineSubtotal = $item->product->price * $item->quantity;
                                $subtotalProduk += $lineSubtotal;
                            @endphp

                            <div class="flex justify-between mb-3 text-sm">
                                <div class="text-slate-600 pr-4">
                                    <span class="font-semibold text-slate-800">
                                        {{ $item->product->name }}
                                    </span>

                                    <div class="text-xs mt-0.5">
                                        {{ $item->quantity }} x Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                    </div>

                                </div>
                                <div class="font-semibold text-slate-900">
                                    Rp {{ number_format($lineSubtotal, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="border-t-2 border-dashed border-slate-100 my-5"></div>

                <div class="flex justify-between mb-2 text-slate-500 text-sm">
                    <span>
                        Subtotal Produk
                    </span>

                    <span class="font-semibold text-slate-900">
                        Rp {{ number_format($subtotalProduk, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between mb-5 text-slate-500 text-sm">
                    <span>
                        Biaya Pengiriman
                    </span>

                    <span class="font-semibold {{ $shipping_cost > 0 ? 'text-slate-900' : 'text-emerald-500'}}">
                        @if($shipping_cost > 0)
                            Rp {{ number_format($shipping_cost, 0, ',', '.') }}
                        @else
                            Belum dihitung
                        @endif
                    </span>
                </div>

                <div class="border-t-2 border-solid border-slate-100 my-5"></div>

                <div class="flex justify-between mb-6 text-lg">
                    <span class="font-extrabold text-slate-900">
                        Total Pembayaran
                    </span>
                    <span class="font-extrabold text-indigo-600">
                        Rp {{ number_format($subtotalProduk + $shipping_cost, 0, ',', '.') }}
                    </span>
                </div>

                @php
                    $isReadyToSubmit =
                        $has_address &&
                        $shipping_cost > 0 &&
                        !empty($courier) &&
                        !empty($shipping_service);
                @endphp

                <button type="submit" form="checkout-form" @if(!$isReadyToSubmit) disabled @endif class="flex justify-center items-center gap-2 w-full p-4 border-none font-bold text-base rounded-lg text-white transition-all duration-200 {{ $isReadyToSubmit ? 'cursor-pointer bg-slate-900 hover:bg-slate-800' : 'cursor-not-allowed bg-slate-400' }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    Selesaikan Pesanan
                </button>

                @if(!$has_address)
                    <div class="text-center text-red-500 text-[13px] font-semibold mt-3">
                        Alamat pengiriman belum diatur.
                    </div>
                @elseif(empty($courier))
                    <div class="text-center text-red-500 text-[13px] font-semibold mt-3">
                        Silakan pilih kurir pengiriman.
                    </div>
                @elseif(empty($shipping_service))
                    <div class="text-center text-red-500 text-[13px] font-semibold mt-3">
                        Silakan pilih layanan pengiriman.
                    </div>
                @elseif($shipping_cost <= 0)
                    <div class="text-center text-red-500 text-[13px] font-semibold mt-3">
                        Biaya pengiriman belum tersedia.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>