<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();
Schedule::command('app:update-delivery')->everyMinute();
// Schedule::command('app:update-delivered')->everyMinute();
Schedule::command('app:update-activation')->everyFiveMinutes();
// Schedule::command('app:update-finished')->everyTenSeconds();
Schedule::command('app:update-billing')->daily();
Schedule::command('app:cancel-order')->daily();
