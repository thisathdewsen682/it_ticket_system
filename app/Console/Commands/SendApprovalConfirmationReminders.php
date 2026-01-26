<?php

namespace App\Console\Commands;

use App\Mail\ApprovalConfirmationReminderMail;
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
                // Format ticket data for the mailable
                $ticketData = $tickets->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'title' => $t->title,
                        'category' => $t->category,
                        'priority' => $t->priority,
                        'requester_name' => $t->requester?->name,
                        'it_member_name' => $t->itMember?->name,
                    ];
                })->toArray();

                Mail::to($approver->email)->send(new ApprovalConfirmationReminderMail(
                    $approver->name,
                    $ticketData
                ));

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
