<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly StoreSetting $store,
        public readonly string $event
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Update pesanan {$this->order->order_no} - {$this->store->store_name}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-status');
    }
}
