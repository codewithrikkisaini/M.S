<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('report:send-daily-cash-sheet')->everyMinute();
Schedule::command('subscriptions:process-expiries')->dailyAt('00:01');
Schedule::command('subscriptions:send-reminders')->dailyAt('09:00');

