<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\EventReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'リマインダー通知を送信する';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        //現在から1分間隔で通知する幅を指定
        $end = $now->copy()->addMinutes(1);

        // 1分間隔で通知するイベントを取得
        $events = Event::where('reminder_sent', false)
            ->whereBetween('reservation_time', [$now, $end])
            ->get();

        // イベントごとに通知を送信してreminder_sentをtrueにする
        foreach ($events as $event) {
            $event->user->notify(new EventReminderNotification($event));
            //reminder_sentをtrueにするように更新
            $event->update(['reminder_sent' => true]);
        }
    }
}
