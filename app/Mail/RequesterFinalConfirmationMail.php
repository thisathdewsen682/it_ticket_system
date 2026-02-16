<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequesterFinalConfirmationMail extends QueuedMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [5];

    public function __construct(
        public Ticket $ticket,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Job Completed & Confirmed - Job #' . $this->ticket->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.requester_final_confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
