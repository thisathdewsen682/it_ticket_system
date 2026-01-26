<?php

namespace App\Console\Commands;

use App\Mail\LongPendingTicketsMail;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendLongPendingReminder extends Command
{
    protected $signature = 'tickets:send-long-pending-reminder';
    protected $description = 'Send reminders about tickets pending for more than 5 days';

    public function handle(): int
    {
        $this->info('Checking for long-pending tickets...');

        // Get tickets created more than 5 days ago and still in progress
        $longPendingTickets = Ticket::query()
            ->with(['requester', 'itMember'])
            ->whereIn('status', ['it_assigned', 'it_reopened'])
            ->whereNotNull('it_member_id')
            ->where('created_at', '<', now()->subDays(5))
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('it_member_id');

        if ($longPendingTickets->isEmpty()) {
            $this->info('No long-pending tickets found.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($longPendingTickets as $memberId => $tickets) {
            $itMember = $tickets->first()->itMember;

            if (!$itMember || !$itMember->email) {
                $this->warn("IT Member #{$memberId}: Missing or no email.");
                continue;
            }

            try {
                Mail::to($itMember->email)->send(new LongPendingTicketsMail($tickets->toArray()));
                $this->info("{$itMember->name}: Long-pending reminder sent ({$tickets->count()} ticket(s))");
                $sent++;
            } catch (\Throwable $e) {
                $this->error("{$itMember->name}: Failed - {$e->getMessage()}");
            }
        }

        $this->info("Summary: {$sent} long-pending reminder(s) sent.");
        return self::SUCCESS;
    }
}
