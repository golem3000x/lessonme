<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Clean up expired temporary guest users daily at 3 AM
Schedule::command('guests:cleanup --force')
    ->daily()
    ->at('03:00')
    ->description('Clean up expired temporary guest users');
