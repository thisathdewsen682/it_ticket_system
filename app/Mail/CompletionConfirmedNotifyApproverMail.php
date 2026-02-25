<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompletionConfirmedNotifyApproverMail extends QueuedMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 5;

    public function __construct(
        public Ticket $ticket,
        public $deptManager = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Job Completed - Notification for Approver - #' . $this->ticket->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.completion_confirmed_notify_approver',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}