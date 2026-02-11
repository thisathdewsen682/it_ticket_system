<?php

namespace App\Console\Commands;

use App\Mail\ItDeptManagerConfirmationReminderMail;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendItDeptManagerConfirmationReminders extends Command
{
    protected $signature = 'tickets:send-it-dept-manager-confirmation-reminders';
    protected $description = 'Send daily reminder emails to IT Department Manager for pending completion confirmations';

    public function handle(): int
    {
        $this->info('Checking for tickets pending IT Department Manager confirmation...');

        // Get all tickets that are IT Manager confirmed and waiting for IT Dept Manager confirmation
        $pendingTickets = Ticket::query()
            ->with(['itMember', 'requester', 'approvalUser'])
            ->where('status', 'it_mgr_confirmed')
            ->get();

        if ($pendingTickets->isEmpty()) {
            $this->info('No pending IT Department Manager confirmations found.');
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
                    Mail::to($itDeptManager->email)->send(new ItDeptManagerConfirmationReminderMail($ticket));
                    $this->info("Sent reminder to {$itDeptManager->name} for ticket #{$ticket->id}");
                    $count++;
                } catch (\Exception $e) {
                    $this->error("Failed to send reminder for ticket #{$ticket->id}: " . $e->getMessage());
                }
            }
        }

        $this->info("Sent {$count} reminder(s) to IT Department Manager(s).");

        return Command::SUCCESS;
    }
}
