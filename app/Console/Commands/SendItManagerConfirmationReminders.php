<?php

namespace App\Console\Commands;

use App\Mail\ItManagerConfirmationReminderMail;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendItManagerConfirmationReminders extends Command
{
    protected $signature = 'tickets:send-it-manager-reminders';
    protected $description = 'Send daily reminder emails to IT Manager for pending confirmations';

    public function handle(): int
    {
        $this->info('Checking for tickets pending IT Manager confirmation...');

        // Get all tickets that are completed and waiting for IT Manager confirmation
        $pendingTickets = Ticket::query()
            ->with(['itMember', 'requester'])
            ->where('status', 'it_completed')
            ->get();

        if ($pendingTickets->isEmpty()) {
            $this->info('No pending confirmations found.');
            return Command::SUCCESS;
        }

        // Get IT Manager
        $itManager = User::whereHas('role', fn($q) => $q->where('name', 'it_manager'))->first();

        if (!$itManager || !$itManager->email) {
            $this->error('IT Manager not found or has no email address.');
            return Command::FAILURE;
        }

        $count = 0;

        foreach ($pendingTickets as $ticket) {
            // Skip if ticket is past due date (already expired)
            if ($ticket->needed_by && now()->greaterThan($ticket->needed_by)) {
                $this->warn("Skipping ticket #{$ticket->id} - past due date");
                continue;
            }

            try {
                Mail::to($itManager->email)->queue(new ItManagerConfirmationReminderMail($ticket));
                $this->info("Sent reminder for ticket #{$ticket->id}");
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for ticket #{$ticket->id}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$count} reminder(s) to IT Manager.");

        return Command::SUCCESS;
    }
}
