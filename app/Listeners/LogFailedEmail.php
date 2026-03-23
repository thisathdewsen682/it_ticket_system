<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSending;

class LogFailedEmail
{
    public function handle(\Illuminate\Queue\Events\JobFailed $event): void
    {
        $job = $event->job;
        $payload = $job->payload();
        $commandData = $payload['data']['command'] ?? null;

        if (!$commandData) {
            return;
        }

        // Check if this is a mail job
        $command = unserialize($commandData);
        if (!$command instanceof \Illuminate\Mail\SendQueuedMailable) {
            return;
        }

        $mailable = $command->mailable;
        $mailableClass = get_class($mailable);

        $to = collect($mailable->to)->map(fn($r) => $r['address'] ?? $r)->implode(', ');
        $from = collect($mailable->from)->map(fn($r) => $r['address'] ?? $r)->implode(', ');

        $ticketId = null;
        if (isset($mailable->ticket) && is_object($mailable->ticket) && isset($mailable->ticket->id)) {
            $ticketId = $mailable->ticket->id;
        }

        EmailLog::create([
            'mailable_class' => $mailableClass,
            'subject' => $mailable->subject ?? class_basename($mailableClass),
            'to' => $to,
            'from' => $from ?: config('mail.from.address'),
            'status' => 'failed',
            'error_message' => $event->exception->getMessage(),
            'ticket_id' => $ticketId,
        ]);
    }
}
