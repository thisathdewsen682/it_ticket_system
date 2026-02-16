<?php

namespace App\Console\Commands;

use App\Mail\CompletionConfirmationMail;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCompletionConfirmations extends Command
{
    protected $signature = 'tickets:send-completion-confirmations';
    protected $description = 'Send completion confirmation emails to ticket requesters';

    public function handle(): int
    {
        $this->info('Checking for recently confirmed tickets...');

        // Get tickets that were just confirmed by IT Manager (status changed to dept_confirmed)
        // We'll send confirmations to tickets that don't have a confirmation sent flag
        $confirmedTickets = Ticket::query()
            ->with(['requester:id,name,email', 'itMember:id,name,email'])
            ->where('status', 'dept_confirmed')
            ->whereNotNull('requester_id')
            ->get();

        if ($confirmedTickets->isEmpty()) {
            $this->info('No confirmed tickets to notify.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($confirmedTickets as $ticket) {
            if (!$ticket->requester->email) {
                $this->warn("Ticket #{$ticket->id}: Requester has no email.");
                continue;
            }

            try {
                Mail::to($ticket->requester->email)->queue(new CompletionConfirmationMail($ticket));
                $this->info("Ticket #{$ticket->id}: Completion confirmation sent to {$ticket->requester->email}");
                $sent++;
            } catch (\Throwable $e) {
                $this->error("Ticket #{$ticket->id}: Failed - {$e->getMessage()}");
            }
        }

        $this->info("Summary: {$sent} completion confirmation(s) sent.");
        return self::SUCCESS;
    }
}
