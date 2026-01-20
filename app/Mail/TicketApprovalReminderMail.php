<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class TicketApprovalReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public string $approveUrl,
        public string $rejectUrl,
        public string $cutoff,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder: Ticket Approval Required - #' . $this->ticket->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.approval_reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }

    /**
     * Generate a signed approve URL that expires at the end of the needed_by day.
     */
    public static function buildApproveUrl(Ticket $ticket): string
    {
        return URL::temporarySignedRoute(
            'tickets.approval_link',
            self::approvalCutoff($ticket),
            ['ticket' => $ticket->id, 'action' => 'approve']
        );
    }

    /**
     * Generate a signed reject URL that expires at the end of the needed_by day.
     */
    public static function buildRejectUrl(Ticket $ticket): string
    {
        return URL::temporarySignedRoute(
            'tickets.approval_link',
            self::approvalCutoff($ticket),
            ['ticket' => $ticket->id, 'action' => 'reject']
        );
    }

    /**
     * Calculate expiration time for approval URLs.
     * Uses end of needed_by day, or 7 days from now if not set.
     */
    public static function approvalCutoff(Ticket $ticket): \DateTimeInterface
    {
        if ($ticket->needed_by) {
            return $ticket->needed_by->copy()->endOfDay();
        }

        return now()->addDays(7);
    }
}
