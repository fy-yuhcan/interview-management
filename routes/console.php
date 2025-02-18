<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 1時間ごとにGoogleカレンダーからイベントを取得してデータベースに保存する
Schedule::command('app:create-google-calendar-events')->hourly();

// 1分ごとにイベントリマインダーを送信する
Schedule::command('app:send-event-reminders')->everyMinute();