<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Event;
use App\Services\OpenAIEventService;
use App\Services\EventCreateService;
use App\Notifications\EventCreatedNotification;
use Mockery;

class SendNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_event_sends_notification()
    {
        //notificationをfake化
        Notification::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        //OpenAIEventServiceのモックを部分的に作成し、getFormattedEventData()だけ固定値を返すようにする
        $sut = Mockery::mock(OpenAIEventService::class)->makePartial();
        $sut->shouldReceive('getFormattedEventData')
            ->once()
            ->andReturn([
                'calendar_id'     => 'primary',
                'title'           => 'AIイベント',
                'start_time'      => '2025-01-01 10:00:00',
                'end_time'        => '2025-01-01 11:00:00',
                'reservation_time' => '2025-01-01 09:45:00',
                'status'          => '予定',
                'url'             => '',
                'detail'          => 'OpenAIが作成したイベント',
            ]);

        //createEventFromPrompt() メソッドは実際のメソッドを呼ぶようにpassthru()を設定
        $sut->shouldReceive('createEventFromPrompt')
            ->once()
            ->passthru();

        // メソッドを実行
        $actual = $sut->createEventFromPrompt('dummy prompt');

        //通知が送られたかをテスト
        Notification::assertSentTo(
            [$user],
            EventCreatedNotification::class,
            function ($notification, $channels) use ($actual) {
                // 通知内の event ID が実際に作成された event の ID と一致するかを確認
                return $notification->event->id === $actual->id;
            }
        );

        //DBにイベントが作成されているか検証
        $this->assertDatabaseHas('events', [
            'user_id'    => $user->id,
            'title'      => 'AIイベント',
            'start_time' => '2025-01-01 10:00:00',
        ]);
    }
}
