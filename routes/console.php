<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Fed-b: pull-based, so this instance decides its own fetch cadence —
// nothing waits on it and no peer is notified either way.
Schedule::command('federation:fetch')->hourly();
