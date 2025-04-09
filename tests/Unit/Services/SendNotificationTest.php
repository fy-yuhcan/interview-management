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
use App\Services\GoogleCalendarService;
use App\Notifications\EventCreatedNotification;
use Mockery;

class SendNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_event_sends_notification()
    {
        Notification::fake();

        $user = User::factory()->create([
            'token' => json_encode(['access_token' => 'dummy-token']),
        ]);
        $this->actingAs($user);

        // GoogleCalendarServiceをモック化（テスト環境で実際のAPI呼び出しを防ぐため）
        $mockGoogleCalendarService = Mockery::mock('overload:' . GoogleCalendarService::class);
        $mockGoogleCalendarService
            ->shouldReceive('setAccessTokenForUser')
            ->once()
            ->andReturnNull();

        $mockGoogleCalendarService
            ->shouldReceive('createEvent')
            ->once()
            ->andReturn(new class {
                public function getId()
                {
                    return 'dummy-google-calendar-id';
                }
            });

        // OpenAIEventServiceを部分的にモック化（getFormattedEventDataのみ固定値）
        $sut = Mockery::mock(OpenAIEventService::class)->makePartial();
        $sut->shouldReceive('getFormattedEventData')
            ->once()
            ->andReturn([
                'title'            => 'AIイベント',
                'start_time'       => '2025-01-01 10:00:00',
                'end_time'         => '2025-01-01 11:00:00',
                'reservation_time' => '2025-01-01 09:45:00',
                'status'           => '予定',
                'url'              => '',
                'detail'           => 'OpenAIが作成したイベント',
            ]);

        // メソッドを実行
        $actual = $sut->createEventFromPrompt('dummy prompt');

        // 通知が送信されたことを確認
        Notification::assertSentTo(
            [$user],
            EventCreatedNotification::class,
            function ($notification, $channels) use ($actual) {
                return $notification->event->id === $actual->id;
            }
        );

        // DBにイベントが作成されたことを検証
        $this->assertDatabaseHas('events', [
            'user_id'    => $user->id,
            'calendar_id' => 'dummy-google-calendar-id',
            'title'      => 'AIイベント',
            'start_time' => '2025-01-01 10:00:00',
        ]);
    }
}
