<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly StoreSetting $store,
        public readonly string $recipientRole = 'customer'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Invoice {$this->order->invoice_no} - {$this->store->store_name}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.invoice');
    }

    public function attachments(): array
    {
        if (! $this->order->invoice?->file_path) {
            return [];
        }

        return [
            Attachment::fromStorageDisk('local', $this->order->invoice->file_path)
                ->as($this->order->invoice_no.'.html')
                ->withMime('text/html'),
        ];
    }
}
