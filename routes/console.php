<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('activity-logs:prune --days=90')->daily()->at('02:00');

// Weekly cleanup of orphaned user records (30+ days old)
Schedule::command('users:cleanup-orphaned --days=30 --force')->weekly()->sundays()->at('03:00');
