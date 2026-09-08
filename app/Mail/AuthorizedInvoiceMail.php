<?php

namespace App\Mail;

use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuthorizedInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Sale $sale,
        public string $signedXml,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Factura electrónica autorizada - ' . $this->sale->sri_access_key,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.authorized-invoice',
        );
    }

    public function attachments(): array
    {
        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $this->signedXml,
                'factura-' . $this->sale->sri_access_key . '.xml'
            )->withMime('application/xml'),
        ];
    }
}
