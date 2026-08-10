<div class="max-w-[1100px] mx-auto py-10 px-5 font-sans">
    @php
        $payment = $order->payments
            ->sortByDesc('created_at')
            ->first();

        $shipment = $order->shipments
            ->sortByDesc('created_at')
            ->first();

        $address = $order->shippingAddress;

        $isPaid = strtolower((string) $order->payment_status) === 'paid';
        $expiryAt = $payment?->expiry_at;
    @endphp

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-7">
        <div>
            <a href="{{ route('orders.history') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-indigo-600 transition-colors duration-200 mb-3">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5"></path>
                    <path d="M12 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Riwayat Pesanan
            </a>

            <h1 class="text-[28px] font-extrabold text-slate-900 m-0">
                Detail Pesanan
            </h1>

            <p class="text-sm text-slate-500 mt-2 mb-0">
                Invoice:
                <span class="font-bold text-slate-700">
                    {{ $order->invoice_no }}
                </span>
            </p>
        </div>

        <div>
            @if($isPaid)
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-50 text-emerald-700 text-sm font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Sudah Dibayar
                </span>
            @else
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-50 text-amber-700 text-sm font-bold">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    Belum Dibayar
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,2fr)_minmax(300px,1fr)] gap-6">
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] p-6">
                <div class="flex items-center justify-between border-b-2 border-slate-100 pb-4 mb-5">
                    <h2 class="text-lg font-extrabold text-slate-800 m-0">
                        Informasi Pesanan
                    </h2>

                    <span class="text-xs font-semibold text-slate-400">
                        {{ $order->created_at?->format('d M Y, H:i') }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            Nomor Invoice
                        </p>

                        <p class="font-bold text-slate-800 m-0">
                            {{ $order->invoice_no }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            Tanggal Pesanan
                        </p>

                        <p class="font-bold text-slate-800 m-0">
                            {{ $order->created_at?->format('d M Y, H:i') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            Status Pesanan
                        </p>

                        <span class="inline-flex px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            Status Pembayaran
                        </p>

                        @if($isPaid)
                            <span class="inline-flex px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">
                                Sudah Dibayar
                            </span>
                        @else
                            <span class="inline-flex px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-bold">
                                Belum Dibayar
                            </span>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            Status Pengiriman
                        </p>

                        <span class="inline-flex px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                            {{ ucfirst($order->delivery_status) }}
                        </span>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            Status Fulfillment
                        </p>

                        <span class="inline-flex px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                            {{ ucfirst($order->fulfillment_status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] p-6">
                <h2 class="text-lg font-extrabold text-slate-800 border-b-2 border-slate-100 pb-4 mb-5">
                    Informasi Pembeli
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            Nama Lengkap
                        </p>

                        <p class="font-semibold text-slate-800 m-0">
                            {{ $order->buyer_name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            Email
                        </p>

                        <p class="font-semibold text-slate-800 m-0 break-all">
                            {{ $order->buyer_email }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            Nomor WhatsApp
                        </p>

                        <p class="font-semibold text-slate-800 m-0">
                            {{ $order->buyer_whatsapp }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] p-6">
                <h2 class="text-lg font-extrabold text-slate-800 border-b-2 border-slate-100 pb-4 mb-5">
                    Produk Pesanan
                </h2>

                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 border border-slate-200 rounded-xl">
                            <div class="min-w-0">
                                <p class="font-bold text-slate-800 m-0">
                                    {{ $item->product_name }}
                                </p>

                                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-xs text-slate-500">
                                    <span>
                                        SKU: {{ $item->sku }}
                                    </span>

                                    <span>
                                        {{ $item->quantity }} ×
                                        Rp {{ number_format($item->product_price, 0, ',', '.') }}
                                    </span>

                                    <span>
                                        {{ number_format($item->weight_grams, 0, ',', '.') }} Gram
                                    </span>
                                </div>
                            </div>

                            <div class="text-left sm:text-right shrink-0">
                                <p class="text-xs text-slate-400 mb-1">
                                    Subtotal
                                </p>

                                <p class="font-extrabold text-slate-900 m-0">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] p-6">
                <h2 class="text-lg font-extrabold text-slate-800 border-b-2 border-slate-100 pb-4 mb-5">
                    Alamat Pengiriman
                </h2>

                @if($address)
                    <div class="p-5 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <p class="font-extrabold text-slate-800 m-0">
                                    {{ $address->receiver_name }}
                                </p>

                                <p class="text-sm text-slate-500 mt-1 mb-3">
                                    {{ $address->phone }}
                                </p>

                                <p class="text-sm text-slate-700 leading-6 m-0">
                                    {{ $address->address_line }}<br>

                                    {{ $address->subdistrict_name }},
                                    {{ $address->district_name }}<br>

                                    {{ $address->city_name }},
                                    {{ $address->province_name }}
                                    {{ $address->postal_code }}
                                </p>

                                @if(!empty($address->courier_note))
                                    <div class="mt-4 pt-4 border-t border-slate-200">
                                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                            Catatan Pengiriman
                                        </p>

                                        <p class="text-sm text-slate-600 m-0">
                                            {{ $address->courier_note }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-5 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                        Alamat pengiriman tidak ditemukan.
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] p-6">
                <h2 class="text-lg font-extrabold text-slate-800 border-b-2 border-slate-100 pb-4 mb-5">
                    Informasi Pengiriman
                </h2>

                @if($shipment)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                Kurir
                            </p>

                            <p class="font-bold text-slate-800 m-0">
                                {{ $shipment->courier_name }}
                            </p>

                            <p class="text-xs text-slate-500 mt-1 mb-0">
                                {{ strtoupper($shipment->courier_code) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                Layanan
                            </p>

                            <p class="font-bold text-slate-800 m-0">
                                {{ $shipment->service_name }}
                            </p>

                            <p class="text-xs text-slate-500 mt-1 mb-0">
                                {{ $shipment->service_code }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                Estimasi Tiba
                            </p>

                            <p class="font-bold text-slate-800 m-0">
                                {{ $shipment->etd ?: '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                Biaya Pengiriman
                            </p>

                            <p class="font-bold text-slate-800 m-0">
                                Rp {{ number_format($shipment->cost, 0, ',', '.') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                Nomor Resi
                            </p>

                            @if($shipment->awb_number)
                                <p class="font-extrabold text-indigo-600 m-0">
                                    {{ $shipment->awb_number }}
                                </p>
                            @else
                                <p class="font-semibold text-slate-400 m-0">
                                    Belum tersedia
                                </p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                Status Pengiriman
                            </p>

                            <span class="inline-flex px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                                {{ ucfirst($shipment->status) }}
                            </span>
                        </div>
                    </div>

                    @if($shipment->shipped_at || $shipment->delivered_at)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5 pt-5 border-t border-slate-100">
                            @if($shipment->shipped_at)
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                        Dikirim Pada
                                    </p>

                                    <p class="text-sm font-semibold text-slate-700 m-0">
                                        {{ $shipment->shipped_at?->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            @endif

                            @if($shipment->delivered_at)
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                        Diterima Pada
                                    </p>

                                    <p class="text-sm font-semibold text-slate-700 m-0">
                                        {{ $shipment->delivered_at?->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif
                @else
                    <div class="p-5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-500">
                        Informasi pengiriman belum tersedia.
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] p-6 lg:sticky lg:top-5">
                <h2 class="text-lg font-extrabold text-slate-800 border-b-2 border-slate-100 pb-4 mb-5">
                    Ringkasan Pembayaran
                </h2>

                <div class="space-y-3">
                    <div class="flex justify-between gap-4 text-sm">
                        <span class="text-slate-500">
                            Subtotal Produk
                        </span>

                        <span class="font-semibold text-slate-800">
                            Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4 text-sm">
                        <span class="text-slate-500">
                            Biaya Pengiriman
                        </span>

                        <span class="font-semibold text-slate-800">
                            Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="border-t border-slate-100 pt-4 mt-4 flex justify-between gap-4">
                        <span class="font-extrabold text-slate-900">
                            Total Pembayaran
                        </span>

                        <span class="font-extrabold text-indigo-600 text-lg">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] p-6">
                <h2 class="text-lg font-extrabold text-slate-800 border-b-2 border-slate-100 pb-4 mb-5">
                    Pembayaran
                </h2>

                @if($payment)
                    @if($isPaid)
                        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6L9 17l-5-5"></path>
                                    </svg>
                                </div>

                                <div>
                                    <p class="font-extrabold text-emerald-800 m-0">
                                        Pembayaran Berhasil
                                    </p>

                                    <p class="text-xs text-emerald-700 mt-1 mb-0">
                                        Pesanan telah dibayar.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                    Metode Pembayaran
                                </p>

                                <p class="font-bold text-slate-800 m-0">
                                    {{ strtoupper($payment->payment_type ?: '-') }}
                                </p>
                            </div>

                            @if($payment->bank)
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                        Bank
                                    </p>

                                    <p class="font-semibold text-slate-700 m-0">
                                        {{ strtoupper($payment->bank) }}
                                    </p>
                                </div>
                            @endif

                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                    Nominal
                                </p>

                                <p class="font-extrabold text-slate-900 m-0">
                                    Rp {{ number_format($payment->gross_amount, 0, ',', '.') }}
                                </p>
                            </div>

                            @if($payment->paid_at)
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                        Dibayar Pada
                                    </p>

                                    <p class="font-semibold text-slate-700 m-0">
                                        {{ $payment->paid_at?->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            @endif

                            @if($payment->transaction_id)
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                        Transaction ID
                                    </p>

                                    <p class="text-sm font-semibold text-slate-700 break-all m-0">
                                        {{ $payment->transaction_id }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <polyline points="12 7 12 12 15 14"></polyline>
                                    </svg>
                                </div>

                                <div>
                                    <p class="font-extrabold text-amber-800 m-0">
                                        Menunggu Pembayaran
                                    </p>

                                    <p class="text-xs text-amber-700 mt-1 mb-0">
                                        Segera selesaikan pembayaran pesanan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($expiryAt)
                            <div wire:ignore data-payment-countdown data-expiry="{{ \Carbon\Carbon::parse($expiryAt)->toIso8601String() }}" class="mb-5 p-4 bg-slate-900 rounded-xl text-center">
                                <p class="text-xs font-semibold text-slate-300 uppercase tracking-wide mb-2">
                                    Batas Waktu Pembayaran
                                </p>

                                <div data-countdown-value class="text-2xl font-extrabold text-white tracking-wider">
                                    Memuat...
                                </div>
                            </div>
                        @endif

                        <div class="space-y-4">
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                    Metode Pembayaran
                                </p>

                                <p class="font-bold text-slate-800 m-0">
                                    {{ strtoupper($payment->payment_type ?: '-') }}
                                </p>
                            </div>

                            @if($payment->bank)
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                        Bank
                                    </p>

                                    <p class="font-semibold text-slate-700 m-0">
                                        {{ strtoupper($payment->bank) }}
                                    </p>
                                </div>
                            @endif

                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                    Total Pembayaran
                                </p>

                                <p class="font-extrabold text-indigo-600 text-lg m-0">
                                    Rp {{ number_format($payment->gross_amount, 0, ',', '.') }}
                                </p>
                            </div>

                            @if($payment->redirect_url)
                                <a href="{{ $payment->redirect_url }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold text-sm no-underline transition-colors duration-200">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                        <path d="M7 7h.01"></path>
                                        <path d="M17 7h.01"></path>
                                        <path d="M7 17h.01"></path>
                                        <path d="M17 17h.01"></path>
                                    </svg>
                                    Lanjutkan Pembayaran
                                </a>
                            @endif

                            @if($order->payment_status === 'pending' && $payment->snap_token)
                                <button type="button" onclick="window.snap.pay(@js($payment->snap_token), {onSuccess: () => window.location.reload(), onPending: () => window.location.reload(), onError: () => window.location.reload(), onClose: () => {}})" class="flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-3 text-sm font-bold text-white">Bayar dengan Midtrans</button>
                            @elseif($order->payment_status === 'pending')
                                <button wire:click="retryPayment" class="flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-3 text-sm font-bold text-white">Muat ulang pembayaran</button>
                            @endif

                            @if($payment->va_number)
                                <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                        Nomor Virtual Account
                                    </p>

                                    <p class="text-lg font-extrabold text-slate-900 tracking-wide m-0">
                                        {{ $payment->va_number }}
                                    </p>
                                </div>
                            @endif

                            @if($payment->bill_key || $payment->biller_code)
                                <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg space-y-3">
                                    @if($payment->biller_code)
                                        <div>
                                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                                Biller Code
                                            </p>

                                            <p class="font-extrabold text-slate-900 m-0">
                                                {{ $payment->biller_code }}
                                            </p>
                                        </div>
                                    @endif

                                    @if($payment->bill_key)
                                        <div>
                                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                                Bill Key
                                            </p>

                                            <p class="font-extrabold text-slate-900 m-0">
                                                {{ $payment->bill_key }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                @else
                    <div class="p-5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-500">
                        Informasi pembayaran belum tersedia.
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] p-6">
                <h2 class="text-lg font-extrabold text-slate-800 border-b-2 border-slate-100 pb-4 mb-5">
                    Status Pesanan
                </h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-slate-500">
                            Pesanan
                        </span>

                        <span class="text-sm font-bold text-slate-800">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-slate-500">
                            Pembayaran
                        </span>

                        <span class="text-sm font-bold {{ $isPaid ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-slate-500">
                            Pengiriman
                        </span>

                        <span class="text-sm font-bold text-slate-800">
                            {{ ucfirst($order->delivery_status) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-slate-500">
                            Fulfillment
                        </span>

                        <span class="text-sm font-bold text-slate-800">
                            {{ ucfirst($order->fulfillment_status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ route('invoices.show', $order) }}" target="_blank" class="mt-5 block rounded-xl bg-slate-900 px-5 py-3 text-center font-bold text-white">Lihat / Cetak Invoice</a>
    </div>
</div>

@if(!$isPaid && $expiryAt)
<script>
    (() => {

        const countdownContainer = document.querySelector(
            '[data-payment-countdown]'
        );

        if (!countdownContainer) {
            return;
        }

        const countdownValue = countdownContainer.querySelector(
            '[data-countdown-value]'
        );

        const expiryString = countdownContainer.dataset.expiry;

        if (!expiryString || !countdownValue) {
            return;
        }

        const expiryTime = new Date(expiryString).getTime();

        const updateCountdown = () => {

            const now = Date.now();
            const remaining = expiryTime - now;

            if (remaining <= 0) {

                countdownValue.textContent = 'Waktu pembayaran habis';

                countdownValue.classList.remove(
                    'text-white'
                );

                countdownValue.classList.add(
                    'text-red-400'
                );

                return;
            }

            const totalSeconds = Math.floor(
                remaining / 1000
            );

            const hours = Math.floor(
                totalSeconds / 3600
            );

            const minutes = Math.floor(
                (totalSeconds % 3600) / 60
            );

            const seconds =
                totalSeconds % 60;

            countdownValue.textContent =
                `${String(hours).padStart(2, '0')}:` +
                `${String(minutes).padStart(2, '0')}:` +
                `${String(seconds).padStart(2, '0')}`;
        };


        updateCountdown();

        const timer = setInterval(() => {

            updateCountdown();

            if (
                expiryTime <= Date.now()
            ) {
                clearInterval(timer);
            }

        }, 1000);

    })();
</script>
@endif

@once
    @push('scripts')
        <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endpush
@endonce
