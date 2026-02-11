<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequesterFinalConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 5;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    public function __construct(
        public Ticket $ticket,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Job Completed & Confirmed - Ticket #' . $this->ticket->id,
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
