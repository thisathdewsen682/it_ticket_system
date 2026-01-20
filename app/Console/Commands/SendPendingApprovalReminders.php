<?php

namespace App\Console\Commands;

use App\Mail\TicketApprovalReminderMail;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPendingApprovalReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:send-approval-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily reminder emails for pending ticket approvals';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for pending approval tickets...');

        // Get all pending tickets where approval is still needed
        $pendingTickets = Ticket::query()
            ->with(['requester:id,name,email', 'approvalUser:id,name,email'])
            ->where('status', 'pending')
            ->whereNotNull('approval_user_id')
            ->whereHas('approvalUser', function ($query) {
                $query->whereNotNull('email');
            })
            ->get();

        if ($pendingTickets->isEmpty()) {
            $this->info('No pending approval tickets found.');
            return self::SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        foreach ($pendingTickets as $ticket) {
            // Skip if approval deadline has already passed
            if ($ticket->needed_by && now()->greaterThan($ticket->needed_by->copy()->endOfDay())) {
                $this->warn("Ticket #{$ticket->id}: Skipped (deadline passed)");
                continue;
            }

            try {
                $approveUrl = TicketApprovalReminderMail::buildApproveUrl($ticket);
                $rejectUrl = TicketApprovalReminderMail::buildRejectUrl($ticket);
                $cutoff = TicketApprovalReminderMail::approvalCutoff($ticket)->format('F j, Y g:i A');

                Mail::to($ticket->approvalUser->email)->send(
                    new TicketApprovalReminderMail($ticket, $approveUrl, $rejectUrl, $cutoff)
                );

                $this->info("Ticket #{$ticket->id}: Reminder sent to {$ticket->approvalUser->email}");
                $sent++;
            } catch (\Throwable $e) {
                $this->error("Ticket #{$ticket->id}: Failed to send - {$e->getMessage()}");
                report($e);
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Summary: {$sent} reminders sent, {$failed} failed.");

        return self::SUCCESS;
    }
}
