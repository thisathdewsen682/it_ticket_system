<?php

namespace App\Console\Commands;

use App\Mail\TicketAssignedToItMemberMail;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAssignedItMembersReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:send-assigned-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily reminder emails to IT members who have assigned tickets';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking tickets assigned to IT members...');

        $tickets = Ticket::query()
            ->with(['itMember', 'requester'])
            ->whereIn('status', ['it_assigned', 'it_reopened'])
            ->whereNotNull('it_member_id')
            ->get();

        if ($tickets->isEmpty()) {
            $this->info('No assigned tickets found.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($tickets as $ticket) {
            $itMember = $ticket->itMember;

            if (!$itMember || !$itMember->email) {
                $this->warn("Ticket #{$ticket->id}: IT member missing or has no email.");
                continue;
            }

            // Skip if past due date
            if ($ticket->needed_by && now()->greaterThan($ticket->needed_by->copy()->endOfDay())) {
                $this->warn("Ticket #{$ticket->id}: Skipped (past due date).");
                continue;
            }

            try {
                Mail::to($itMember->email)->send(new TicketAssignedToItMemberMail($ticket));
                $this->info("Ticket #{$ticket->id}: Reminder sent to {$itMember->email}");
                $sent++;
            } catch (\Throwable $e) {
                $this->error("Ticket #{$ticket->id}: Failed to send - {$e->getMessage()}");
                report($e);
            }
        }

        $this->info("Summary: {$sent} reminder(s) sent.");

        return self::SUCCESS;
    }
}
