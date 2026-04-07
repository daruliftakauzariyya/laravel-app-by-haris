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
    ->everyThreeDays()
    ->at('08:00')
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping()
    ->description('Send pending fatwa reminder to muftis');
