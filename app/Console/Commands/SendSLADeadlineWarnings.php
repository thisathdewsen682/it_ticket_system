<?php

namespace App\Console\Commands;

use App\Mail\SLADeadlineWarningMail;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendSLADeadlineWarnings extends Command
{
    protected $signature = 'tickets:send-sla-warnings';
    protected $description = 'Send SLA deadline warnings 2 days before deadline';

    public function handle(): int
    {
        $this->info('Checking for tickets approaching SLA deadline...');

        // Get tickets with deadline in 2 days
        $twodays_from_now = now()->copy()->addDays(2)->endOfDay();
        $three_days_from_now = now()->copy()->addDays(3)->startOfDay();

        $tickets = Ticket::query()
            ->with(['requester', 'itMember', 'approvalUser'])
            ->whereIn('status', ['pending', 'dept_approved', 'it_assigned', 'it_reopened', 'dept_reopened', 'requester_reopened', 'it_completed'])
            ->whereNotNull('needed_by')
            ->whereBetween('needed_by', [now(), $twodays_from_now])
            ->get();

        if ($tickets->isEmpty()) {
            $this->info('No tickets approaching SLA deadline.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($tickets as $ticket) {
            $daysRemaining = max(0, now()->diffInDays($ticket->needed_by));
            $recipient = $ticket->requester->email;

            if (!$recipient) {
                $this->warn("Ticket #{$ticket->id}: Requester has no email.");
                continue;
            }

            try {
                Mail::to($recipient)->queue(new SLADeadlineWarningMail($ticket, $daysRemaining));
                $this->info("Ticket #{$ticket->id}: SLA warning sent to {$recipient}");
                $sent++;
            } catch (\Throwable $e) {
                $this->error("Ticket #{$ticket->id}: Failed - {$e->getMessage()}");
            }
        }

        $this->info("Summary: {$sent} SLA warning(s) sent.");
        return self::SUCCESS;
    }
}
