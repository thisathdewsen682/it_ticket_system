<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequesterConfirmationReminderMail extends QueuedMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $requesterName,
        public array $tickets,
    ) {
    }

    public function envelope(): Envelope
    {
        $count = count($this->tickets);
        return new Envelope(
            subject: "REMINDER: {$count} Job(s) Awaiting Your Confirmation",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.requester_confirmation_reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
