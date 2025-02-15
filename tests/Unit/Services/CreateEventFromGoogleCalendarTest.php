<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\User;
use App\Services\EventCreateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateEventFromGoogleCalendarTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Googleカレンダーから取得したイベントをDBに保存できるかのテスト
     */
    public function test_create_event_from_Google_calendar(): void
    {
        $user = User::factory()->create([
            'email' => 'test@test'
        ]);
        $this->actingAs($user);

        //fakeオブジェクトを使ってGoogleカレンダーAPIをモック化
        $fakeGoogleEvent = new Class{
            public function getTile(){
                return '会議';
            }
            public function getStart(){
                return '2025-02-20 10:00:00';
            }
            public function getEnd(){
                return '2025-02-20 11:00:00';
            }
            public function getReservationTime(){
                return '2025-02-20 09:00:00';
            }
            public function getStatus(){
                return '予定';
            }
            public function getUrl(){
                return 'https://example.com';
            }
            public function getDetail(){
                return 'プロジェクト進捗会議';
            }
        };
        

        $formattedResponse = [
            'title'      => $fakeGoogleEvent->getTile(),
            'start_time' => $fakeGoogleEvent->getStart(),
            'end_time'   => $fakeGoogleEvent->getEnd(),
            'reservation_time' => $fakeGoogleEvent->getReservationTime(),
            'status'     => $fakeGoogleEvent->getStatus(),
            'url'        => $fakeGoogleEvent->getUrl(),
            'detail'     => $fakeGoogleEvent->getDetail(),
        ];

        $service = new EventCreateService();

        //createEventメソッドを実行
        $event = $service->createEvent($formattedResponse, $user->id);

        //DBに保存されたイベントが期待通りか確認
        $this->assertDatabaseHas('events', [
            'user_id' => $user->id,
            'title' => $formattedResponse['title'],
            'start_time' => $formattedResponse['start_time'],
            'end_time' => $formattedResponse['end_time'],
            'reservation_time' => $formattedResponse['reservation_time'],
            'status' => $formattedResponse['status'],
            'url' => $formattedResponse['url'],
            'detail' => $formattedResponse['detail'],
        ]);

        $this->assertInstanceOf(Event::class, $event);

    }
}
