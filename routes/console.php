<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily reminder emails for pending approvals
// Runs every day at 9:00 AM
Schedule::command('tickets:send-approval-reminders')
    ->dailyAt('09:00')
    ->withoutOverlapping()
    ->onOneServer();

// Schedule daily reminder emails to assigned IT members
// Runs every day at 9:10 AM
Schedule::command('tickets:send-assigned-reminder')
    ->dailyAt('09:02')
    ->withoutOverlapping()
    ->onOneServer();

// Schedule daily reminder emails for unassigned approvd tickets
// Runs every day at 8:30 AM
Schedule::command('tickets:send-unassigned-reminder')
    ->dailyAt('08:30')
    ->withoutOverlapping()
    ->onOneServer();

// Schedule daily reminder emails for IT Manager confirmations
// Runs every day at 10:00 AM
Schedule::command('tickets:send-it-manager-reminders')
    ->dailyAt('10:00')
    ->withoutOverlapping()
    ->onOneServer();
