<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================================
// Mufti Pending Reminder — runs every 3 days
// ============================================================
Schedule::command('mufti:send-reminders')
    ->cron('0 8 */3 * *')
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping()
    ->description('Send pending fatwa reminder to muftis');

// ============================================================
// Fatwa View Snapshots — every 45 days
// ============================================================
Schedule::command('fatwa:snapshot-views')
    ->cron('0 0 */45 * *')
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping()
    ->description('Snapshot fatwa view counts every 45 days');
