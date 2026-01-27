<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApprovalConfirmationReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $approverName,
        public array $tickets,
    ) {
    }

    public function envelope(): Envelope
    {
        $count = count($this->tickets);
        return new Envelope(
            subject: "ACTION REQUIRED: {$count} Ticket(s) Awaiting Your Confirmation",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.approval_confirmation_reminder',
            with: [
                'approverName' => $this->approverName,
                'tickets' => $this->tickets,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
