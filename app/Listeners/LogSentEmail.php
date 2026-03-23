<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\SentMessage;

class LogSentEmail
{
    public function handle(MessageSent $event): void
    {
        $message = $event->message;
        $mailable = $event->data['__laravel_notification'] ?? $event->data['__mailable'] ?? null;

        $to = $this->formatAddresses($message->getTo());
        $cc = $this->formatAddresses($message->getCc());
        $bcc = $this->formatAddresses($message->getBcc());
        $from = $this->formatAddresses($message->getFrom());

        $ticketId = null;
        $mailableClass = null;

        if ($mailable) {
            $mailableClass = get_class($mailable);
            // Extract ticket_id if the mailable has a public $ticket property
            if (isset($mailable->ticket) && is_object($mailable->ticket) && isset($mailable->ticket->id)) {
                $ticketId = $mailable->ticket->id;
            }
        }

        EmailLog::create([
            'mailable_class' => $mailableClass,
            'subject' => $message->getSubject(),
            'to' => $to,
            'cc' => $cc ?: null,
            'bcc' => $bcc ?: null,
            'from' => $from,
            'status' => 'sent',
            'ticket_id' => $ticketId,
        ]);
    }

    private function formatAddresses(?array $addresses): string
    {
        if (empty($addresses)) {
            return '';
        }

        return collect($addresses)
            ->map(fn($address) => $address->getAddress())
            ->implode(', ');
    }
}
