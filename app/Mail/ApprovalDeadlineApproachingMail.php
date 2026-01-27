<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApprovalDeadlineApproachingMail extends Mailable implements ShouldQueue
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
            subject: 'Urgent: Approval Deadline Approaching - Ticket #' . $this->ticket->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.approval_deadline_approaching',
            with: [
                'ticket' => $this->ticket,
                'daysRemaining' => $this->daysRemaining,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
