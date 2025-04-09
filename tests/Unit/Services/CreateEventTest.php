<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\User;
use App\Services\EventCreateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateEventTest extends TestCase
{
    use RefreshDatabase;
    /**
     * イベントを作成できるかのテスト
     */
    public function test_create_event(): void
    {
        $user  = User::factory()->create();

        $fakeGoogleEvent = new class {
            public function getId()
            {
                return 'dummy-google-event-id';
            }
        };

        $fakeEvent = [
            'title'      => '会議',
            'start_time' => '2025-02-20 10:00:00',
            'end_time'   => '2025-02-20 11:00:00',
            'reservation_time' => '2025-02-20 09:00:00',
            'status'     => '予定',
            'url'        => 'https://example.com',
            'detail'     => 'プロジェクト進捗会議',
        ];

        $sut = new EventCreateService();

        $actual = $sut->createEvent($fakeEvent, $user->id, $fakeGoogleEvent);

        $this->assertDatabaseHas('events', [
            'user_id'    => $user->id,
            'title'      => '会議',
            'start_time' => '2025-02-20 10:00:00',
            'end_time'   => '2025-02-20 11:00:00',
            'reservation_time' => '2025-02-20 09:00:00',
            'status'     => '予定',
            'url'        => 'https://example.com',
            'detail'     => 'プロジェクト進捗会議',
        ]);

        $this->assertInstanceOf(Event::class, $actual);
    }
}
