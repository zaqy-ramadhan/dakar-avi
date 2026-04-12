<?php

use App\Console\Commands\SendContractExpiryReminder;
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
