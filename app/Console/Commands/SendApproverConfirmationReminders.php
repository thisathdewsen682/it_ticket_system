<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendApproverConfirmationReminders extends Command
{
    protected $signature = 'tickets:send-approver-confirmation-reminders';
    protected $description = 'Send daily reminder to approvers about their confirmed tickets';

    public function handle(): int
    {
        $this->info('Checking for confirmed tickets to notify approvers...');

        // Get all unique approvers who have confirmed tickets
        $approversWithConfirmedTickets = Ticket::query()
            ->with(['approvalUser:id,name,email', 'requester:id,name,email'])
            ->where('status', 'it_mgr_confirmed')
            ->whereNotNull('approval_user_id')
            ->whereHas('approvalUser', fn($q) => $q->whereNotNull('email'))
            ->get()
            ->groupBy('approval_user_id');

        if ($approversWithConfirmedTickets->isEmpty()) {
            $this->info('No confirmed tickets found.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($approversWithConfirmedTickets as $approverId => $tickets) {
            $approver = $tickets->first()->approvalUser;

            if (!$approver || !$approver->email) {
                $this->warn("Approver #{$approverId}: Missing or no email.");
                continue;
            }

            try {
                $ticketList = $tickets->map(function ($t) {
                    return "- Ticket #{$t->id}: {$t->title} (Requester: {$t->requester?->name})";
                })->implode("\n");

                Mail::raw(
                    "Hello {$approver->name},\n\n" .
                    "This is a reminder that {$tickets->count()} ticket(s) you approved have been confirmed completed by IT Manager:\n\n" .
                    $ticketList .
                    "\n\nThese tickets are now fully resolved.",
                    function ($message) use ($approver, $tickets) {
                        $message->to($approver->email)
                            ->subject("Confirmation Reminder: {$tickets->count()} Ticket(s) Completed");
                    }
                );

                $this->info("Approver reminder sent to {$approver->name} for {$tickets->count()} ticket(s)");
                $sent++;
            } catch (\Throwable $e) {
                $this->error("Failed to send reminder to {$approver->name}: {$e->getMessage()}");
            }
        }

        $this->info("Summary: {$sent} approver reminder(s) sent.");
        return self::SUCCESS;
    }
}
