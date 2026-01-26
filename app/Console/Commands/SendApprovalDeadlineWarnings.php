<?php

namespace App\Console\Commands;

use App\Mail\ApprovalDeadlineApproachingMail;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendApprovalDeadlineWarnings extends Command
{
    protected $signature = 'tickets:send-approval-deadline-warnings';
    protected $description = 'Send warnings when approval deadline is 2 days away';

    public function handle(): int
    {
        $this->info('Checking for tickets with approaching approval deadlines...');

        // Get pending tickets with deadline in 2 days
        $tickets = Ticket::query()
            ->with(['requester:id,name,email', 'approvalUser:id,name,email'])
            ->where('status', 'pending')
            ->whereNotNull('approval_user_id')
            ->whereNotNull('needed_by')
            ->whereHas('approvalUser', function ($q) {
                $q->whereNotNull('email');
            })
            ->get()
            ->filter(function ($ticket) {
                $daysUntilDeadline = now()->diffInDays($ticket->needed_by);
                return $daysUntilDeadline === 2;
            });

        if ($tickets->isEmpty()) {
            $this->info('No approvals with 2-day deadline.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($tickets as $ticket) {
            $daysRemaining = now()->diffInDays($ticket->needed_by);

            try {
                Mail::to($ticket->approvalUser->email)->send(
                    new ApprovalDeadlineApproachingMail($ticket, $daysRemaining)
                );
                $this->info("Ticket #{$ticket->id}: Approval deadline warning sent");
                $sent++;
            } catch (\Throwable $e) {
                $this->error("Ticket #{$ticket->id}: Failed - {$e->getMessage()}");
            }
        }

        $this->info("Summary: {$sent} approval deadline warning(s) sent.");
        return self::SUCCESS;
    }
}
