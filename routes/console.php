<?php

use App\Services\BorrowService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('sendDueNotification', function () {
    BorrowService::sendDueNotification();
})
    ->schedule()
    ->everyMinute();
