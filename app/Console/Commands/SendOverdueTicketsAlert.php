<?php

namespace App\Console\Commands;

use App\Mail\OverdueTicketsMail;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendOverdueTicketsAlert extends Command
{
    protected $signature = 'tickets:send-overdue-alerts';
    protected $description = 'Send escalation alerts for overdue tickets to IT Manager';

    public function handle(): int
    {
        $this->info('Checking for overdue tickets...');

        // Get all overdue tickets
        $overdueTickets = Ticket::query()
            ->with(['requester', 'itMember', 'approvalUser'])
            ->whereIn('status', ['pending', 'dept_approved', 'it_assigned', 'it_reopened', 'dept_reopened', 'requester_reopened', 'it_completed'])
            ->whereNotNull('needed_by')
            ->where('needed_by', '<', now())
            ->orderBy('needed_by', 'asc')
            ->get();

        if ($overdueTickets->isEmpty()) {
            $this->info('No overdue tickets found.');
            return self::SUCCESS;
        }

        // Get IT Manager
        $itManager = User::whereHas('role', fn($q) => $q->where('name', 'it_manager'))->first();

        if (!$itManager || !$itManager->email) {
            $this->error('IT Manager not found or has no email.');
            return self::FAILURE;
        }

        try {
            Mail::to($itManager->email)->send(
                new OverdueTicketsMail($overdueTickets->toArray())
            );
            $this->info("Overdue tickets alert sent to IT Manager: {$itManager->email}");
            $this->info("Total overdue tickets: " . $overdueTickets->count());
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to send overdue alert: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
