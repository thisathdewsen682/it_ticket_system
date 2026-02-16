<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SLADeadlineWarningMail extends QueuedMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public int $daysRemaining,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SLA Deadline Warning - Job #' . $this->ticket->id . ' - ' . $this->daysRemaining . ' days remaining',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.sla_deadline_warning',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
