<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketRejectedNotifyRequesterMail extends QueuedMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public string $remark,
        public ?string $rejectedBy = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Job was Rejected - #' . $this->ticket->id,
        );
    }

    public function content(): Content
    {
        $resolvedRejectedBy = $this->resolveRejectedBy();
        return new Content(
            view: 'emails.ticket_rejected_notify_requester',
            with: [
                'ticket' => $this->ticket,
                'remark' => $this->remark,
                'rejectedBy' => $resolvedRejectedBy,
            ],
        );
    }

    private function resolveRejectedBy(): string
    {
        if ($this->rejectedBy) {
            return $this->rejectedBy;
        }

        $this->ticket->loadMissing([
            'statusHistories.user.roles',
            'statusHistories.user.role',
        ]);

        $history = $this->ticket->statusHistories
            ->sortByDesc('id')
            ->first(fn($h) => in_array($h->to_status, ['dept_rejected', 'it_dept_rejected', 'it_manager_rejected'], true));

        $user = $history?->user;
        $name = $user?->name ?? 'Manager';

        $roleName = $user?->roles?->first()?->name
            ?? $user?->role?->name;

        return match ($roleName) {
            'it_manager' => 'IT Manager: ' . $name,
            'it-dept-manager' => 'IT Department Manager: ' . $name,
            'dept_manager', 'section_manager' => 'Department/Section Manager: ' . $name,
            default => 'Manager: ' . $name,
        };
    }

    public function attachments(): array
    {
        return [];
    }
}
