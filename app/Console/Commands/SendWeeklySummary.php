<?php

namespace App\Console\Commands;

use App\Mail\WeeklySummaryMail;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWeeklySummary extends Command
{
    protected $signature = 'tickets:send-weekly-summary';
    protected $description = 'Send weekly ticket summary to department managers';

    public function handle(): int
    {
        $this->info('Generating and sending weekly summaries...');

        // Get all unique sections that have tickets
        $sectionsWithTickets = Ticket::distinct('section_id')
            ->whereNotNull('section_id')
            ->pluck('section_id')
            ->toArray();

        if (empty($sectionsWithTickets)) {
            $this->info('No tickets with assigned sections found.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($sectionsWithTickets as $sectionId) {
            // Get all department managers and find one for this section
            $managers = User::whereHas('role', fn($q) => $q->where('name', 'dept_manager'))
                ->whereNotNull('email')
                ->get();

            // For now, send to the first available dept_manager
            // In a real scenario, you might have a manager_id field in sections table
            $manager = $managers->first();
            
            if (!$manager) {
                $this->warn("No department manager found for section ID {$sectionId}.");
                continue;
            }

            // Get tickets for this section
            $summary = [
                'manager' => $manager->name,
                'section_id' => $sectionId,
                'total' => Ticket::where('section_id', $sectionId)->count(),
                'pending' => Ticket::where('section_id', $sectionId)->where('status', 'pending')->count(),
                'approved' => Ticket::where('section_id', $sectionId)->where('status', 'dept_approved')->count(),
                'in_progress' => Ticket::where('section_id', $sectionId)->whereIn('status', ['it_assigned', 'it_reopened'])->count(),
                'completed' => Ticket::where('section_id', $sectionId)->where('status', 'dept_confirmed')->whereBetween('updated_at', [now()->subDays(7), now()])->count(),
                'overdue' => Ticket::where('section_id', $sectionId)->whereIn('status', ['pending', 'dept_approved', 'it_assigned', 'it_reopened'])->where('needed_by', '<', now())->count(),
            ];

            try {
                Mail::to($manager->email)->send(new WeeklySummaryMail($summary));
                $this->info("Weekly summary sent to {$manager->name} for section ID {$sectionId}");
                $sent++;
            } catch (\Throwable $e) {
                $this->error("Failed to send summary for section {$sectionId}: {$e->getMessage()}");
            }
        }

        $this->info("Summary: {$sent} weekly summary(ies) sent.");
        return self::SUCCESS;
    }
}
