<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendApprovalConfirmationReminders extends Command
{
    protected $signature = 'tickets:send-approval-confirmation-reminders';
    protected $description = 'Send daily reminder to approvers to confirm IT Manager completed tickets';

    public function handle(): int
    {
        $this->info('Checking for tickets pending approver confirmation...');

        // Get tickets that IT Manager has confirmed but approver hasn't confirmed yet
        $pendingConfirmationTickets = Ticket::query()
            ->with(['approvalUser:id,name,email', 'requester:id,name,email', 'itMember:id,name,email'])
            ->where('status', 'it_mgr_confirmed')
            ->whereNotNull('approval_user_id')
            ->whereHas('approvalUser', fn($q) => $q->whereNotNull('email'))
            ->get();

        if ($pendingConfirmationTickets->isEmpty()) {
            $this->info('No tickets pending approver confirmation.');
            return self::SUCCESS;
        }

        // Group by approver
        $ticketsByApprover = $pendingConfirmationTickets->groupBy('approval_user_id');

        $sent = 0;
        foreach ($ticketsByApprover as $approverId => $tickets) {
            $approver = $tickets->first()->approvalUser;

            if (!$approver || !$approver->email) {
                $this->warn("Approver #{$approverId}: Missing or no email.");
                continue;
            }

            try {
                $ticketList = $tickets->map(function ($t) {
                    return "- Ticket #{$t->id}: {$t->title} (Requester: {$t->requester?->name}) - Completed by: {$t->itMember?->name}";
                })->implode("\n");

                Mail::raw(
                    "Hello {$approver->name},\n\n" .
                    "This is a reminder that the following {$tickets->count()} ticket(s) have been completed by IT Manager and are waiting for your confirmation:\n\n" .
                    $ticketList .
                    "\n\nPlease review and confirm these tickets are resolved, or reopen them if more work is needed.",
                    function ($message) use ($approver, $tickets) {
                        $message->to($approver->email)
                            ->subject("ACTION REQUIRED: {$tickets->count()} Ticket(s) Awaiting Your Confirmation");
                    }
                );

                $this->info("Approver confirmation reminder sent to {$approver->name} for {$tickets->count()} ticket(s)");
                $sent++;
            } catch (\Throwable $e) {
                $this->error("Failed to send reminder to {$approver->name}: {$e->getMessage()}");
            }
        }

        $this->info("Summary: {$sent} approver confirmation reminder(s) sent.");
        return self::SUCCESS;
    }
}
