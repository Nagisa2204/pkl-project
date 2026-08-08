<div class="max-w-[1200px] mx-auto py-8 px-5 font-sans">
    @php
        $payment = $order->payments
            ->sortByDesc('created_at')
            ->first();

        $shipment = $order->shipments
            ->sortByDesc('created_at')
            ->first();

        $address = $order->shippingAddress;

        $isPaid = strtolower((string) $order->payment_status) === 'paid';
    @endphp

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-7">
        <div>
        <a
            href="{{ route('admin.orders') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-indigo-600 transition-colors duration-200 mb-3 no-underline"
        >
            <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
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


    {{-- STATUS UTAMA --}}
    <div>

        @php
            $status = strtolower((string) $order->status);

            $statusClass = match ($status) {
                'completed',
                'complete',
                'selesai' =>
                    'bg-emerald-50 text-emerald-700',

                'cancelled',
                'canceled',
                'dibatalkan' =>
                    'bg-red-50 text-red-700',

                'processing',
                'diproses' =>
                    'bg-blue-50 text-blue-700',

                'shipped',
                'dikirim' =>
                    'bg-indigo-50 text-indigo-700',

                default =>
                    'bg-amber-50 text-amber-700',
            };
        @endphp

        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold {{ $statusClass }}">

            <span class="w-2 h-2 rounded-full bg-current"></span>

            {{ ucfirst($order->status) }}

        </span>

    </div>

</div>


{{-- ================================================================
     GRID
================================================================= --}}
<div class="grid grid-cols-1 lg:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)] gap-6">


    {{-- ============================================================
         KOLOM KIRI
    ============================================================= --}}
    <div class="space-y-6">


        {{-- ========================================================
             INFORMASI ORDER
        ========================================================= --}}
        <div class="bg-white rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] p-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b-2 border-slate-100 pb-4 mb-5">

                <h2 class="text-lg font-extrabold text-slate-800 m-0">
                    Informasi Pesanan
                </h2>

                <span class="text-xs font-semibold text-slate-400">
                    {{ $order->created_at?->format('d M Y, H:i') }}
                </span>

            </div>


            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                        Invoice
                    </p>

                    <p class="font-bold text-slate-800 m-0 break-all">
                        {{ $order->invoice_no }}
                    </p>
                </div>


                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                        Tanggal Order
                    </p>

                    <p class="font-semibold text-slate-700 m-0">
                        {{ $order->created_at?->format('d M Y, H:i') }}
                    </p>
                </div>


                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                        Status
                    </p>

                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $statusClass }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>


                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                        Status Pembayaran
                    </p>

                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold
                        {{ $isPaid
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'bg-amber-50 text-amber-700'
                        }}"
                    >
                        {{ ucfirst($order->payment_status) }}
                    </span>
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
                        Fulfillment
                    </p>

                    <span class="inline-flex px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                        {{ ucfirst($order->fulfillment_status) }}
                    </span>
                </div>

            </div>

        </div>


        {{-- ========================================================
             CUSTOMER
        ========================================================= --}}
        <div class="bg-white rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] p-6">

            <h2 class="text-lg font-extrabold text-slate-800 border-b-2 border-slate-100 pb-4 mb-5">
                Informasi Customer
            </h2>


            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                        Nama
                    </p>

                    <p class="font-bold text-slate-800 m-0">
                        {{ $order->buyer_name }}
                    </p>
                </div>


                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                        Email
                    </p>

                    <p class="font-semibold text-slate-700 break-all m-0">
                        {{ $order->buyer_email }}
                    </p>
                </div>


                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                        WhatsApp
                    </p>

                    <p class="font-semibold text-slate-700 m-0">
                        {{ $order->buyer_whatsapp }}
                    </p>
                </div>


                @if($order->user)

                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            User ID
                        </p>

                        <p class="font-semibold text-slate-700 m-0">
                            #{{ $order->user->id }}
                        </p>
                    </div>

                @endif

            </div>

        </div>


        {{-- ========================================================
             PRODUK
        ========================================================= --}}
        <div class="bg-white rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] p-6">

            <div class="flex items-center justify-between border-b-2 border-slate-100 pb-4 mb-5">

                <h2 class="text-lg font-extrabold text-slate-800 m-0">
                    Produk Pesanan
                </h2>

                <span class="text-xs font-semibold text-slate-400">
                    {{ $order->items->count() }} produk
                </span>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full min-w-[700px] text-sm">

                    <thead>

                        <tr class="border-b border-slate-200">

                            <th class="text-left py-3 pr-4 text-xs font-bold text-slate-400 uppercase tracking-wide">
                                Produk
                            </th>

                            <th class="text-left py-3 px-4 text-xs font-bold text-slate-400 uppercase tracking-wide">
                                SKU
                            </th>

                            <th class="text-right py-3 px-4 text-xs font-bold text-slate-400 uppercase tracking-wide">
                                Harga
                            </th>

                            <th class="text-center py-3 px-4 text-xs font-bold text-slate-400 uppercase tracking-wide">
                                Qty
                            </th>

                            <th class="text-right py-3 pl-4 text-xs font-bold text-slate-400 uppercase tracking-wide">
                                Subtotal
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($order->items as $item)

                            <tr class="border-b border-slate-100 last:border-b-0">

                                <td class="py-4 pr-4">

                                    <p class="font-bold text-slate-800 m-0">
                                        {{ $item->product_name }}
                                    </p>

                                    <p class="text-xs text-slate-400 mt-1 mb-0">
                                        Berat:
                                        {{ number_format($item->weight_grams, 0, ',', '.') }}
                                        gram
                                    </p>

                                </td>


                                <td class="py-4 px-4">

                                    <span class="font-medium text-slate-600">
                                        {{ $item->sku }}
                                    </span>

                                </td>


                                <td class="py-4 px-4 text-right">

                                    <span class="font-semibold text-slate-700">
                                        Rp {{ number_format($item->product_price, 0, ',', '.') }}
                                    </span>

                                </td>


                                <td class="py-4 px-4 text-center">

                                    <span class="inline-flex min-w-8 justify-center px-2 py-1 rounded-md bg-slate-100 text-slate-700 font-bold">
                                        {{ $item->quantity }}
                                    </span>

                                </td>


                                <td class="py-4 pl-4 text-right">

                                    <span class="font-extrabold text-slate-900">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>


        {{-- ========================================================
             ALAMAT PENGIRIMAN
        ========================================================= --}}
        <div class="bg-white rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] p-6">

            <h2 class="text-lg font-extrabold text-slate-800 border-b-2 border-slate-100 pb-4 mb-5">
                Alamat Pengiriman
            </h2>


            @if($address)

                <div class="p-5 bg-slate-50 border border-slate-200 rounded-xl">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>

                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                Penerima
                            </p>

                            <p class="font-extrabold text-slate-800 m-0">
                                {{ $address->receiver_name }}
                            </p>

                        </div>


                        <div>

                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                Nomor Telepon
                            </p>

                            <p class="font-semibold text-slate-700 m-0">
                                {{ $address->phone }}
                            </p>

                        </div>


                        <div class="md:col-span-2">

                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                Alamat
                            </p>

                            <p class="text-sm text-slate-700 leading-6 m-0">
                                {{ $address->address_line }}<br>
                                {{ $address->subdistrict_name }},
                                {{ $address->district_name }}<br>
                                {{ $address->city_name }},
                                {{ $address->province_name }}
                                {{ $address->postal_code }}
                            </p>

                        </div>


                        @if($address->destination_label)

                            <div>

                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                    Destination
                                </p>

                                <p class="text-sm font-semibold text-slate-700 m-0">
                                    {{ $address->destination_label }}
                                </p>

                            </div>

                        @endif


                        @if($address->courier_note)

                            <div>

                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                    Catatan Kurir
                                </p>

                                <p class="text-sm text-slate-700 m-0">
                                    {{ $address->courier_note }}
                                </p>

                            </div>

                        @endif

                    </div>

                </div>

            @else

                <div class="p-5 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    Alamat pengiriman tidak ditemukan.
                </div>

            @endif

        </div>


        {{-- ========================================================
             SHIPMENT
        ========================================================= --}}
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
                            Ongkir
                        </p>

                        <p class="font-bold text-slate-800 m-0">
                            Rp {{ number_format($shipment->cost, 0, ',', '.') }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            Estimasi
                        </p>

                        <p class="font-bold text-slate-800 m-0">
                            {{ $shipment->etd ?: '-' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            Nomor Resi / AWB
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
                            Status Shipment
                        </p>

                        <span class="inline-flex px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                            {{ ucfirst($shipment->status) }}
                        </span>

                    </div>

                </div>


                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5 pt-5 border-t border-slate-100">

                    <div>

                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            Dikirim Pada
                        </p>

                        <p class="text-sm font-semibold text-slate-700 m-0">
                            {{ $shipment->shipped_at?->format('d M Y, H:i') ?? '-' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            Diterima Pada
                        </p>

                        <p class="text-sm font-semibold text-slate-700 m-0">
                            {{ $shipment->delivered_at?->format('d M Y, H:i') ?? '-' }}
                        </p>

                    </div>

                </div>

            @else

                <div class="p-5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-500">
                    Informasi shipment belum tersedia.
                </div>

            @endif

        </div>

    </div>


    {{-- ============================================================
         KOLOM KANAN
    ============================================================= --}}
    <div class="space-y-6">


        {{-- ========================================================
             RINGKASAN PEMBAYARAN
        ========================================================= --}}
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
                        Total
                    </span>

                    <span class="font-extrabold text-indigo-600 text-lg">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </span>

                </div>

            </div>

        </div>


        {{-- ========================================================
             DETAIL PEMBAYARAN
        ========================================================= --}}
        <div class="bg-white rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] p-6">

            <h2 class="text-lg font-extrabold text-slate-800 border-b-2 border-slate-100 pb-4 mb-5">
                Detail Pembayaran
            </h2>


            @if($payment)

                <div class="space-y-4">

                    <div>

                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            Provider
                        </p>

                        <p class="font-bold text-slate-800 m-0">
                            {{ strtoupper($payment->provider) }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            Payment Type
                        </p>

                        <p class="font-semibold text-slate-700 m-0">
                            {{ strtoupper($payment->payment_type) }}
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
                            Status Payment
                        </p>

                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold
                            {{ $isPaid
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-amber-50 text-amber-700'
                            }}"
                        >
                            {{ ucfirst($payment->status) }}
                        </span>

                    </div>


                    <div>

                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                            Gross Amount
                        </p>

                        <p class="font-extrabold text-slate-900 m-0">
                            Rp {{ number_format($payment->gross_amount, 0, ',', '.') }}
                        </p>

                    </div>


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


                    @if($payment->provider_order_id)

                        <div>

                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                Provider Order ID
                            </p>

                            <p class="text-sm font-semibold text-slate-700 break-all m-0">
                                {{ $payment->provider_order_id }}
                            </p>

                        </div>

                    @endif


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


                    @if($payment->expiry_at)

                        <div>

                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                Batas Pembayaran
                            </p>

                            <p class="font-semibold text-slate-700 m-0">
                                {{ $payment->expiry_at?->format('d M Y, H:i') }}
                            </p>

                        </div>

                    @endif


                    @if($payment->va_number)

                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">

                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">
                                Virtual Account
                            </p>

                            <p class="font-extrabold text-slate-900 tracking-wide m-0">
                                {{ $payment->va_number }}
                            </p>

                        </div>

                    @endif


                    @if($payment->biller_code || $payment->bill_key)

                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">

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

            @else

                <div class="p-5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-500">
                    Data pembayaran belum tersedia.
                </div>

            @endif

        </div>


        {{-- ========================================================
             UPDATE STATUS
        ========================================================= --}}
        <div class="bg-white rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] p-6">

            <h2 class="text-lg font-extrabold text-slate-800 border-b-2 border-slate-100 pb-4 mb-5">
                Kelola Status Pesanan
            </h2>


            <div class="space-y-3">

                <p class="text-sm text-slate-500 m-0">
                    Status saat ini:
                    <span class="font-bold text-slate-700">
                        {{ ucfirst($order->status) }}
                    </span>
                </p>


                <div class="grid grid-cols-1 gap-2">

                    @foreach([
                        'pending' => 'Menunggu',
                        'processing' => 'Diproses',
                        'shipped' => 'Dikirim',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ] as $value => $label)

                        @if(strtolower((string) $order->status) !== $value)

                            <button
                                type="button"
                                wire:click="updateStatus('{{ $value }}')"
                                wire:confirm="Apakah kamu yakin ingin mengubah status pesanan menjadi {{ $label }}?"
                                class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm transition-colors duration-200"
                            >
                                {{ $label }}
                            </button>

                        @endif

                    @endforeach

                </div>

            </div>

        </div>


        {{-- ========================================================
             DATA INTERNAL
        ========================================================= --}}
        <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6">

            <h2 class="text-sm font-extrabold text-slate-700 mb-4">
                Informasi Internal
            </h2>


            <div class="space-y-3 text-sm">

                <div class="flex justify-between gap-4">

                    <span class="text-slate-500">
                        Order ID
                    </span>

                    <span class="font-semibold text-slate-700">
                        #{{ $order->id }}
                    </span>

                </div>


                <div class="flex justify-between gap-4">

                    <span class="text-slate-500">
                        User ID
                    </span>

                    <span class="font-semibold text-slate-700">
                        #{{ $order->user_id }}
                    </span>

                </div>


                <div class="flex justify-between gap-4">

                    <span class="text-slate-500">
                        Address ID
                    </span>

                    <span class="font-semibold text-slate-700">
                        #{{ $order->shipping_address_id }}
                    </span>

                </div>


                @if($order->paid_at)

                    <div class="flex justify-between gap-4">

                        <span class="text-slate-500">
                            Paid At
                        </span>

                        <span class="font-semibold text-slate-700 text-right">
                            {{ $order->paid_at?->format('d M Y, H:i') }}
                        </span>

                    </div>

                @endif


                @if($order->stock_reserved_at)

                    <div class="flex justify-between gap-4">

                        <span class="text-slate-500">
                            Stock Reserved
                        </span>

                        <span class="font-semibold text-slate-700 text-right">
                            {{ $order->stock_reserved_at?->format('d M Y, H:i') }}
                        </span>

                    </div>

                @endif


                @if($order->stock_released_at)

                    <div class="flex justify-between gap-4">

                        <span class="text-slate-500">
                            Stock Released
                        </span>

                        <span class="font-semibold text-slate-700 text-right">
                            {{ $order->stock_released_at?->format('d M Y, H:i') }}
                        </span>

                    </div>

                @endif


                @if($order->stock_shortage_at)

                    <div class="flex justify-between gap-4">

                        <span class="text-slate-500">
                            Stock Shortage
                        </span>

                        <span class="font-semibold text-slate-700 text-right">
                            {{ $order->stock_shortage_at?->format('d M Y, H:i') }}
                        </span>

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>
```

</div>
