<?php

namespace App\Console\Commands;

use App\Mail\ApprovalConfirmationReminderMail;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendApproverConfirmationReminders extends Command
{
    protected $signature = 'tickets:send-approver-confirmation-reminders';
    protected $description = 'Send daily reminder to approvers about their confirmed tickets';

    public function handle(): int
    {
        $this->info('Checking for tickets pending approver confirmation...');

        // Get all unique approvers who have confirmed tickets
        $approversWithConfirmedTickets = Ticket::query()
            ->with(['approvalUser:id,name,email', 'requester:id,name,email', 'itMember:id,name,email'])
            ->whereIn('status', ['it_mgr_confirmed', 'it_dept_confirmed_completion'])
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

                Mail::to($approver->email)->queue(new ApprovalConfirmationReminderMail(
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
