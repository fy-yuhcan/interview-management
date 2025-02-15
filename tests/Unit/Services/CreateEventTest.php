<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class CreateEventTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_create_event(): void
    {
        $user  = User::factory()->create();

        $fakeEvent = [
            'title'      => '会議',
            'start_time' => '2025-02-20 10:00:00',
            'end_time'   => '2025-02-20 11:00:00',
            'reservation_time' => '2025-02-20 09:00:00',
            'status'     => '予定',
            'url'        => 'https://example.com',
            'detail'     => 'プロジェクト進捗会議',
        ];

        $service = new EventCreateService();

        $createdEvent = $service->createEvent($fakeEvent, $user->id);

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

        $this->assertInstanceOf(Event::class, $createdEvent);
    }
}
