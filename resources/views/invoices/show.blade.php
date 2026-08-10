<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $order->invoice_no }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #1f2937; margin: 32px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #111827; padding-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th, td { padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        th { background: #f3f4f6; }
        .right { text-align: right; }
        .totals { width: 380px; margin-left: auto; }
        .muted { color: #6b7280; font-size: 13px; }
        @media print { .no-print { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">Cetak / Simpan PDF</button>
    <div class="header">
        <div>
            <h1>{{ $store->store_name }}</h1>
            <div>{{ $store->address }}</div>
            <div>{{ collect([$store->city, $store->province, $store->postal_code])->filter()->implode(', ') }}</div>
            <div>{{ $store->email }} {{ $store->whatsapp ? ' · '.$store->whatsapp : '' }}</div>
        </div>
        <div class="right">
            <h2>INVOICE</h2>
            <strong>{{ $order->invoice_no }}</strong><br>
            <span class="muted">Order: {{ $order->order_no }}</span><br>
            <span class="muted">{{ $order->created_at->format('d/m/Y H:i T') }}</span>
        </div>
    </div>

    <p><strong>Customer:</strong> {{ $order->buyer_name }} ({{ $order->buyer_email }})</p>
    @php($shipment = $order->shipments->first())
    @if($shipment)
        <p><strong>Pengiriman:</strong> {{ $shipment->receiver_name }}, {{ $shipment->address_line }},
            {{ $shipment->subdistrict_name }}, {{ $shipment->district_name }}, {{ $shipment->city_name }},
            {{ $shipment->province_name }} {{ $shipment->postal_code }}<br>
            {{ $shipment->courier_name }} {{ $shipment->service_name }} · Estimasi {{ $shipment->etd ?: '-' }}</p>
    @endif

    <table>
        <thead><tr><th>Produk</th><th>SKU</th><th class="right">Harga</th><th class="right">Qty</th><th class="right">Subtotal</th></tr></thead>
        <tbody>
        @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}<div class="muted">{{ $item->variant_name }}</div></td>
                <td>{{ $item->sku }}</td>
                <td class="right">Rp {{ number_format($item->product_price, 0, ',', '.') }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td class="right">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td></tr>
        <tr><td>Diskon</td><td class="right">- Rp {{ number_format($order->discount_total, 0, ',', '.') }}</td></tr>
        <tr><td>Ongkir</td><td class="right">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td></tr>
        <tr><td>Biaya pembayaran</td><td class="right">Rp {{ number_format($order->payment_fee, 0, ',', '.') }}</td></tr>
        <tr><th>Total</th><th class="right">Rp {{ number_format($order->total, 0, ',', '.') }}</th></tr>
    </table>
    <p><strong>Metode:</strong> {{ strtoupper($order->payment_method) }} · <strong>Status pembayaran:</strong> {{ strtoupper($order->payment_status) }}</p>
    <p class="muted">Invoice ini dibuat otomatis oleh {{ $store->store_name }}.</p>
</body>
</html>
