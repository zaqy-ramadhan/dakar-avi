<?php

use App\Console\Commands\SendContractExpiryReminder;
use App\Console\Commands\SendActivityLogDailySummary;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule contract expiry reminder email
// Runs on the first day of every month at 08:00
Schedule::command(SendContractExpiryReminder::class)
    ->monthlyOn(1, '08:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Schedule daily activity log summary email
// Runs every day at 08:30 for previous day's activities
Schedule::command(SendActivityLogDailySummary::class)
    ->dailyAt('08:30')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();
