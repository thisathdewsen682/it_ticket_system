<?php

namespace App\Console\Commands;

use App\Mail\SLADeadlineWarningMail;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendUnassignedTicketDeadlineWarning extends Command
{
    protected $signature = 'tickets:send-unassigned-deadline-warning';
    protected $description = 'Send urgent deadline warning for unassigned approved tickets (2 days before deadline)';

    public function handle(): int
    {
        $this->info('Checking for unassigned approved tickets approaching deadline...');

        // Get approved tickets with deadline in 2 days that haven't been assigned to IT member
        $tickets = Ticket::query()
            ->with(['requester', 'approvalUser'])
            ->whereIn('status', ['dept_approved', 'dept_reopened'])
            ->whereNull('it_member_id')
            ->whereNotNull('needed_by')
            ->get()
            ->filter(function ($ticket) {
                $daysUntilDeadline = now()->diffInDays($ticket->needed_by);
                return $daysUntilDeadline === 2;
            });

        if ($tickets->isEmpty()) {
            $this->info('No unassigned tickets with 2-day deadline.');
            return self::SUCCESS;
        }

        // Get IT Manager
        $itManager = User::whereHas('role', fn($q) => $q->where('name', 'it_manager'))->first();

        if (!$itManager || !$itManager->email) {
            $this->error('IT Manager not found or has no email.');
            return self::FAILURE;
        }

        try {
            $summary = [
                'count' => $tickets->count(),
                'tickets' => $tickets->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'title' => $t->title,
                        'requester' => $t->requester?->name,
                        'needed_by' => $t->needed_by?->format('F j, Y'),
                        'days_remaining' => now()->diffInDays($t->needed_by),
                    ];
                })->toArray(),
            ];

            Mail::raw(
                "URGENT: {$tickets->count()} approved ticket(s) still unassigned with deadline in 2 DAYS!\n\n" .
                "Please assign IT member(s) immediately:\n\n" .
                collect($summary['tickets'])->map(fn($t) => 
                    "- Ticket #{$t['id']}: {$t['title']} (Requester: {$t['requester']}) - Due: {$t['needed_by']}"
                )->implode("\n"),
                function ($message) use ($itManager) {
                    $message->to($itManager->email)
                        ->subject('URGENT: Unassigned Approved Tickets Approaching Deadline');
                }
            );

            $this->info("Urgent deadline warning sent to IT Manager for {$tickets->count()} ticket(s)");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to send warning: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
