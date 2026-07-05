<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Commands
|--------------------------------------------------------------------------
*/

app(Schedule::class)
    ->command('followup:reminder')
    ->dailyAt('09:00');

app(Schedule::class)
    ->command('app:generate-sitemap')
    ->daily();

/*
|--------------------------------------------------------------------------
| Demo Database Reset
|--------------------------------------------------------------------------
*/

app(Schedule::class)
    ->command('demo:reset')
    ->dailyAt('00:00'); // Every day at 12:00 AM