<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReopenedByDeptManagerMail extends QueuedMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket, public string $remark)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Job Reopened by Manager - #' . $this->ticket->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket_reopened_by_dept_manager',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
