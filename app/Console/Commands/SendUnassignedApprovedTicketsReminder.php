<?php

namespace App\Console\Commands;

use App\Mail\UnassignedApprovedTicketsReminderMail;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendUnassignedApprovedTicketsReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:send-unassigned-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily reminder to IT Manager about approved tickets not yet assigned to IT members';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find IT Manager
        $itManager = User::whereHas('role', function ($query) {
            $query->where('name', 'it_manager');
        })->first();

        if (!$itManager) {
            $this->error('IT Manager not found');
            return 1;
        }

        // Get all approved tickets that haven't been assigned to IT members
        $unassignedTickets = Ticket::with(['requester', 'approvalUser'])
            ->whereIn('status', ['dept_approved', 'it_reopened'])
            ->whereNull('it_member_id')
            ->orderBy('created_at', 'asc')
            ->get();

        // Only send email if there are unassigned tickets
        if ($unassignedTickets->isEmpty()) {
            $this->info('No unassigned approved tickets found. No reminder sent.');
            return 0;
        }

        // Send reminder email
        Mail::to($itManager->email)->send(new UnassignedApprovedTicketsReminderMail($unassignedTickets));

        $this->info("Reminder sent to IT Manager ({$itManager->email}) for {$unassignedTickets->count()} unassigned ticket(s).");

        return 0;
    }
}
