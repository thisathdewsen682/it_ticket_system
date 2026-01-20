<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class TicketApprovalRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public string $approveUrl,
        public string $rejectUrl,
        public ?Carbon $approvalCutoff,
    ) {
    }

    public static function buildApproveUrl(Ticket $ticket): string
    {
        $expiresAt = $ticket->needed_by
            ? Carbon::parse($ticket->needed_by)->endOfDay()
            : now()->addDays(7);

        return URL::temporarySignedRoute(
            'tickets.approval_link',
            $expiresAt,
            ['ticket' => $ticket->id, 'action' => 'approve']
        );
    }

    public static function buildRejectUrl(Ticket $ticket): string
    {
        $expiresAt = $ticket->needed_by
            ? Carbon::parse($ticket->needed_by)->endOfDay()
            : now()->addDays(7);

        return URL::temporarySignedRoute(
            'tickets.approval_link',
            $expiresAt,
            ['ticket' => $ticket->id, 'action' => 'reject']
        );
    }

    public static function approvalCutoff(Ticket $ticket): ?Carbon
    {
        return $ticket->needed_by
            ? Carbon::parse($ticket->needed_by)->endOfDay()
            : null;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Approval Required: Ticket #' . $this->ticket->id . ' - ' . $this->ticket->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.approval_request',
            with: [
                'ticket' => $this->ticket,
                'approveUrl' => $this->approveUrl,
                'rejectUrl' => $this->rejectUrl,
                'approvalCutoff' => $this->approvalCutoff,
            ],
        );
    }
}
