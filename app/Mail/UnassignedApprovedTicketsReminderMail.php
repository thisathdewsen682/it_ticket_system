<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class UnassignedApprovedTicketsReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collection $tickets,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder: ' . $this->tickets->count() . ' Tickets Awaiting Assignment/Reassignment',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.unassigned_approved_tickets_reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
