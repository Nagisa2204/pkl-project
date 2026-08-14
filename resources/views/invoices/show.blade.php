<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $order->invoice_no }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: transparent !important; }
            .invoice-sheet { border: 0 !important; box-shadow: none !important; margin: 0 !important; max-width: none !important; }
        }
    </style>
</head>
<body class="bg-canvas p-5 sm:p-8">
    <main class="invoice-sheet ui-card mx-auto max-w-5xl p-5 sm:p-8">
        <div class="no-print mb-5 flex justify-end">
            <x-ui.button onclick="window.print()">Cetak / Simpan PDF</x-ui.button>
        </div>

        <header class="flex flex-col justify-between gap-6 border-b-2 border-dark pb-5 sm:flex-row">
            <div>
                <h1 class="text-2xl font-extrabold text-content">{{ $store->store_name }}</h1>
                <p class="mt-2 text-sm text-muted">{{ $store->address }}</p>
                <p class="text-sm text-muted">{{ collect([$store->city, $store->province, $store->postal_code])->filter()->implode(', ') }}</p>
                <p class="text-sm text-muted">{{ $store->email }} {{ $store->whatsapp ? ' · '.$store->whatsapp : '' }}</p>
            </div>
            <div class="sm:text-right">
                <h2 class="text-xl font-extrabold text-content">INVOICE</h2>
                <strong class="text-primary">{{ $order->invoice_no }}</strong>
                <p class="mt-1 text-xs text-muted">Pesanan: {{ $order->order_no }}</p>
                <p class="text-xs text-muted">{{ $order->created_at->format('d/m/Y H:i T') }}</p>
            </div>
        </header>

        <section class="mt-5 grid gap-5 text-sm md:grid-cols-2">
            <div><strong class="text-content">Pelanggan</strong><p class="mt-1 text-muted">{{ $order->buyer_name }}<br>{{ $order->buyer_email }}</p></div>
            @php($shipment = $order->shipments->first())
            @if($shipment)
                <div><strong class="text-content">Pengiriman</strong><p class="mt-1 text-muted">{{ $shipment->receiver_name }}, {{ $shipment->address_line }}, {{ $shipment->subdistrict_name }}, {{ $shipment->district_name }}, {{ $shipment->city_name }}, {{ $shipment->province_name }} {{ $shipment->postal_code }}<br>{{ $shipment->courier_name }} {{ $shipment->service_name }} · Estimasi {{ $shipment->etd ?: '-' }}</p></div>
            @endif
        </section>

        <div class="ui-table-wrap mt-6 rounded-xl border border-default">
            <table class="ui-table">
                <thead><tr><th>Produk</th><th>SKU</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th></tr></thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr><td>{{ $item->product_name }}<div class="text-xs text-muted">{{ $item->variant_name }}</div></td><td>{{ $item->sku }}</td><td>Rp {{ number_format($item->product_price, 0, ',', '.') }}</td><td>{{ $item->quantity }}</td><td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 ml-auto max-w-sm space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-muted">Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span class="text-muted">Diskon</span><span>- Rp {{ number_format($order->discount_total, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span class="text-muted">Ongkir</span><span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span class="text-muted">Biaya pembayaran</span><span>Rp {{ number_format($order->payment_fee, 0, ',', '.') }}</span></div>
            <div class="flex justify-between border-t border-default pt-3 text-lg font-extrabold"><span>Total</span><span class="text-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</span></div>
        </div>

        <div class="mt-6 flex flex-wrap gap-2">
            <x-ui.badge variant="info">Metode: {{ strtoupper(str_replace('_', ' ', $order->payment_method)) }}</x-ui.badge>
            <x-ui.badge :variant="$order->payment_status->badgeVariant()">Pembayaran: {{ $order->payment_status->label() }}</x-ui.badge>
        </div>
        <p class="mt-5 text-xs text-muted">Invoice ini dibuat otomatis oleh {{ $store->store_name }}.</p>
    </main>
</body>
</html>
