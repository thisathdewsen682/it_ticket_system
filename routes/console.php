<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/*

//for testing purposes only

Schedule::command('tickets:send-approval-reminders')
    ->dailyAt('17:00')
    ->withoutOverlapping()
    ->onOneServer();

// Schedule daily reminder emails for unassigned approved tickets
// Runs every day at 8:00 AM
Schedule::command('tickets:send-unassigned-reminder')
    ->dailyAt('17:03')
    ->withoutOverlapping()
    ->onOneServer();


    // Schedule daily reminder emails to assigned IT members
// Runs every day at 8:04 AM
Schedule::command('tickets:send-assigned-reminder')
    ->dailyAt('17:05')
    ->withoutOverlapping()
    ->onOneServer();

// Schedule daily reminder emails for IT Manager confirmations
// Runs every day at 8:06 AM
Schedule::command('tickets:send-it-manager-reminders')
    ->dailyAt('17:07')
    ->withoutOverlapping()
    ->onOneServer();

// Schedule daily reminder emails to approvers about confirmed tickets
// Runs every day at 8:07 AM
Schedule::command('tickets:send-approver-confirmation-reminders')
    ->dailyAt('17:09')
    ->withoutOverlapping()
    ->onOneServer();



    // Schedule completion confirmations to requesters
// Runs every day at 8:14 AM
Schedule::command('tickets:send-completion-confirmations')
    ->dailyAt('17:12')
    ->withoutOverlapping()
    ->onOneServer();


// Schedule deadline warning for unassigned approved tickets (2 days before deadline)
// Runs every day at 8:01 AM
Schedule::command('tickets:send-unassigned-deadline-warning')
    ->dailyAt('17:15')
    ->withoutOverlapping()
    ->onOneServer();


// Schedule SLA deadline warnings (2 days before deadline)
// Runs every day at 8:08 AM
Schedule::command('tickets:send-sla-warnings')
    ->dailyAt('17:17')
    ->withoutOverlapping()
    ->onOneServer();

// Schedule approval deadline warnings (2 days before deadline)
// Runs every day at 8:10 AM
Schedule::command('tickets:send-approval-deadline-warnings')
    ->dailyAt('17:19')
    ->withoutOverlapping()
    ->onOneServer();

// Schedule overdue tickets alert
// Runs every day at 8:12 AM
Schedule::command('tickets:send-overdue-alerts')
    ->dailyAt('17:21')
    ->withoutOverlapping()
    ->onOneServer();

*/
// // Schedule daily reminder to approvers to confirm completed tickets
// // Runs every day at 8:15 AM
// Schedule::command('tickets:send-approval-confirmation-reminders')
//     ->dailyAt('08:23')
//     ->withoutOverlapping()
//     ->onOneServer();

// Schedule daily reminder to requesters to confirm or reopen approved tickets
// Runs every day at 8:17 AM
// Schedule::command('tickets:send-requester-confirmation-reminders')
//     ->dailyAt('08:25')
//     ->withoutOverlapping()
//     ->onOneServer();































// Schedule daily reminder emails for pending approvals
// Runs every day at 8:02 AM




Schedule::command('tickets:send-approval-reminders')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->onOneServer();

// Schedule daily reminder emails for unassigned approved tickets
// Runs every day at 8:00 AM
Schedule::command('tickets:send-unassigned-reminder')
    ->dailyAt('08:03')
    ->withoutOverlapping()
    ->onOneServer();


    // Schedule daily reminder emails to assigned IT members
// Runs every day at 8:04 AM
Schedule::command('tickets:send-assigned-reminder')
    ->dailyAt('08:05')
    ->withoutOverlapping()
    ->onOneServer();

// Schedule daily reminder emails for IT Manager confirmations
// Runs every day at 8:06 AM
Schedule::command('tickets:send-it-manager-reminders')
    ->dailyAt('08:07')
    ->withoutOverlapping()
    ->onOneServer();

// Schedule daily reminder emails to approvers about confirmed tickets
// Runs every day at 8:07 AM
Schedule::command('tickets:send-approver-confirmation-reminders')
    ->dailyAt('08:09')
    ->withoutOverlapping()
    ->onOneServer();



    // Schedule completion confirmations to requesters
// Runs every day at 8:14 AM
Schedule::command('tickets:send-completion-confirmations')
    ->dailyAt('08:12')
    ->withoutOverlapping()
    ->onOneServer();


// Schedule deadline warning for unassigned approved tickets (2 days before deadline)
// Runs every day at 8:01 AM
Schedule::command('tickets:send-unassigned-deadline-warning')
    ->dailyAt('08:15')
    ->withoutOverlapping()
    ->onOneServer();


// Schedule SLA deadline warnings (2 days before deadline)
// Runs every day at 8:08 AM
Schedule::command('tickets:send-sla-warnings')
    ->dailyAt('08:17')
    ->withoutOverlapping()
    ->onOneServer();

// Schedule approval deadline warnings (2 days before deadline)
// Runs every day at 8:10 AM
Schedule::command('tickets:send-approval-deadline-warnings')
    ->dailyAt('08:19')
    ->withoutOverlapping()
    ->onOneServer();

// Schedule overdue tickets alert
// Runs every day at 8:12 AM
Schedule::command('tickets:send-overdue-alerts')
    ->dailyAt('08:21')
    ->withoutOverlapping()
    ->onOneServer();


// // Schedule daily reminder to approvers to confirm completed tickets
// // Runs every day at 8:15 AM
// Schedule::command('tickets:send-approval-confirmation-reminders')
//     ->dailyAt('08:23')
//     ->withoutOverlapping()
//     ->onOneServer();

// Schedule daily reminder to requesters to confirm or reopen approved tickets
// Runs every day at 8:17 AM
// Schedule::command('tickets:send-requester-confirmation-reminders')
//     ->dailyAt('08:25')
//     ->withoutOverlapping()
//     ->onOneServer();






// Schedule long-pending tickets reminder (5+ days in progress)
// Runs every day at 8:18 AM
Schedule::command('tickets:send-long-pending-reminder')
    ->dailyAt('08:27')
    ->withoutOverlapping()
    ->onOneServer();

// Schedule weekly summary to department managers
// Runs every Friday at 8:30 AM
Schedule::command('tickets:send-weekly-summary')
    ->weeklyOn(5, '08:30')
    ->withoutOverlapping()
    ->onOneServer();
