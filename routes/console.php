<?php

use App\Jobs\ObjectLogJob;
use App\Jobs\RemoveSoftDeleteJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::job(RemoveSoftDeleteJob::class)->daily();
Schedule::job(ObjectLogJob::class)->everySixHours();
