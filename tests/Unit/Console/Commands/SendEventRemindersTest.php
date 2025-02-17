<?php

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Console\Commands\SendEventReminders;
use App\Notifications\EventReminderNotification;

class SendEventRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_reminders_sends_notification()
    {
        //Notificationをフェイク
        Notification::fake();

        // 現在時刻を固定
        $now = Carbon::now();

        //テスト用ユーザーとイベントを作成
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'user_id'        => $user->id,
            'reservation_time' => $now->copy()->addSeconds(30),
            'start_time'     => $now->copy()->addMinutes(10),
            'end_time'       => $now->copy()->addMinutes(20),
            'reminder_sent'  => false,
        ]);

        //コマンドクラスのインスタンスを生成
        $command = new SendEventReminders();

        //コマンドのhandle()を呼び出す
        $command->handle();

        //通知が送られたか検証
        Notification::assertSentTo(
            [$user],
            EventReminderNotification::class,
            function ($notification, $channels) use ($event) {
                return $notification->event->id === $event->id;
            }
        );

        //reminder_sentがtrueになっているか検証
        $this->assertTrue($event->fresh()->reminder_sent);
    }
}
