@php
    $messages = [
        'created' => 'Pesanan telah dibuat dan menunggu pembayaran.',
        'paid' => 'Pembayaran berhasil dan pesanan sedang diproses.',
        'processing' => 'Pesanan sedang disiapkan oleh toko.',
        'failed' => 'Pembayaran gagal. Stok yang dipesan telah dilepas kembali.',
        'expired' => 'Waktu pembayaran habis dan pesanan dibatalkan.',
        'shipped' => 'Pesanan sudah dikirim.',
        'completed' => 'Pesanan sudah diterima dan selesai.',
        'cancelled' => 'Pesanan telah dibatalkan.',
    ];
@endphp
<p>Halo {{ $order->buyer_name }},</p>
<p>{{ $messages[$event] ?? 'Status pesanan Anda telah diperbarui.' }}</p>
<p>Nomor order: <strong>{{ $order->order_no }}</strong><br>Total: <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></p>
<p>Terima kasih,<br>{{ $store->store_name }}</p>
