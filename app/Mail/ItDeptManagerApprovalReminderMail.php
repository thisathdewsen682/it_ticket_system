<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ItDeptManagerApprovalReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⏰ Reminder: Confirm Approved Ticket - Ticket #' . $this->ticket->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.it_dept_manager_approval_reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
