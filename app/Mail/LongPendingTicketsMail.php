<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LongPendingTicketsMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $tickets,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Alert: ' . count($this->tickets) . ' Ticket(s) Pending for More Than 5 Days',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.long_pending_tickets',
            with: [
                'tickets' => $this->tickets,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
