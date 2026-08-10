<p>Halo {{ $recipientRole === 'admin' ? 'Admin '.$store->store_name : $order->buyer_name }},</p>
<p>Pembayaran untuk pesanan <strong>{{ $order->order_no }}</strong> telah berhasil. Invoice <strong>{{ $order->invoice_no }}</strong> terlampir.</p>
<p>Total pembayaran: <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong>.</p>
<p>Terima kasih,<br>{{ $store->store_name }}</p>
