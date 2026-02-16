<?php

namespace App\Console\Commands;

use App\Mail\ItDeptManagerApprovalReminderMail;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendItDeptManagerApprovalReminders extends Command
{
    protected $signature = 'tickets:send-it-dept-manager-approval-reminders';
    protected $description = 'Send daily reminder emails to IT Department Manager for pending approval confirmations';

    public function handle(): int
    {
        $this->info('Checking for tickets pending IT Department Manager approval confirmation...');

        // Get all tickets that are department approved and waiting for IT Dept Manager confirmation
        $pendingTickets = Ticket::query()
            ->with(['requester', 'approvalUser'])
            ->where('status', 'dept_approved')
            ->get();

        if ($pendingTickets->isEmpty()) {
            $this->info('No pending IT Department Manager approval confirmations found.');
            return Command::SUCCESS;
        }

        // Get IT Department Managers
        $itDeptManagers = User::whereHas('roles', function ($query) {
            $query->where('name', 'it-dept-manager');
        })->get();

        if ($itDeptManagers->isEmpty()) {
            $this->error('No IT Department Managers found.');
            return Command::FAILURE;
        }

        $count = 0;

        foreach ($itDeptManagers as $itDeptManager) {
            if (!$itDeptManager->email) {
                $this->warn("IT Dept Manager {$itDeptManager->name} has no email address.");
                continue;
            }

            // Get tickets relevant to this IT Dept Manager
            $relevantTickets = $pendingTickets;

            if ($relevantTickets->isEmpty()) {
                continue;
            }

            foreach ($relevantTickets as $ticket) {
                // Skip if ticket is past due date (already expired)
                if ($ticket->needed_by && now()->greaterThan($ticket->needed_by)) {
                    $this->warn("Skipping ticket #{$ticket->id} - past due date");
                    continue;
                }

                try {
                    Mail::to($itDeptManager->email)->queue(new ItDeptManagerApprovalReminderMail($ticket));
                    $this->info("Sent approval reminder to {$itDeptManager->name} for ticket #{$ticket->id}");
                    $count++;
                } catch (\Exception $e) {
                    $this->error("Failed to send reminder for ticket #{$ticket->id}: " . $e->getMessage());
                }
            }
        }

        $this->info("Sent {$count} approval reminder(s) to IT Department Manager(s).");

        return Command::SUCCESS;
    }
}
