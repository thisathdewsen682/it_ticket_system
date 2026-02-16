<?php

namespace App\Console\Commands;

use App\Mail\RequesterConfirmationReminderMail;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendRequesterConfirmationReminders extends Command
{
    protected $signature = 'tickets:send-requester-confirmation-reminders';
    protected $description = 'Send daily reminder to requesters to confirm or reopen approved tickets';

    public function handle(): int
    {
        $this->info('Checking for tickets pending requester confirmation...');

        // Get tickets that have been confirmed by dept manager but requester hasn't confirmed yet
        // Status: dept_confirmed = Department manager has confirmed the work is done
        $pendingConfirmationTickets = Ticket::query()
            ->with(['requester:id,name,email', 'approvalUser:id,name,email', 'itMember:id,name,email'])
            ->where('status', 'dept_confirmed')
            ->whereNotNull('requester_id')
            ->whereHas('requester', fn($q) => $q->whereNotNull('email'))
            ->get();

        if ($pendingConfirmationTickets->isEmpty()) {
            $this->info('No tickets pending requester confirmation.');
            return self::SUCCESS;
        }

        // Group by requester
        $ticketsByRequester = $pendingConfirmationTickets->groupBy('requester_id');

        $sent = 0;
        foreach ($ticketsByRequester as $requesterId => $tickets) {
            $requester = $tickets->first()->requester;

            if (!$requester || !$requester->email) {
                $this->warn("Requester #{$requesterId}: Missing or no email.");
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
                        'approval_user_name' => $t->approvalUser?->name,
                        'it_member_name' => $t->itMember?->name,
                    ];
                })->toArray();

                Mail::to($requester->email)->queue(new RequesterConfirmationReminderMail(
                    $requester->name,
                    $ticketData
                ));

                $this->info("Requester confirmation reminder sent to {$requester->name} for {$tickets->count()} ticket(s)");
                $sent++;
            } catch (\Throwable $e) {
                $this->error("Failed to send reminder to {$requester->name}: {$e->getMessage()}");
            }
        }

        $this->info("Summary: {$sent} requester confirmation reminder(s) sent.");
        return self::SUCCESS;
    }
}
