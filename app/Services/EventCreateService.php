<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventCreateService
{
    public $googleCalendarService;
    public function __construct(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }

    public function createEvent($formattedResponse, $userId)
    {
        if (empty($formattedResponse['calendar_id'])) {
            //ここでcalendar_idを設定する
            $createdEvent = $this->googleCalendarService->createEvent($formattedResponse);
            $formattedResponse['calendar_id'] = $createdEvent->getId();
        }

        return Event::firstOrCreate(
            [
                'user_id' => $userId,
                'calendar_id' => $formattedResponse['calendar_id']
            ],
            [
                'user_id' => $userId,
                //TODO:優先度はあとで実装
                'priority_id' => $formattedResponse['priority_id'] ?? null,
                'title' => $formattedResponse['title'] ?? null,
                'start_time' => $formattedResponse['start_time'] ?? null,
                'end_time' => $formattedResponse['end_time'] ?? null,
                'reservation_time' => $formattedResponse['reservation_time'] ?? null,
                'status' => $formattedResponse['status'] ?? null,
                'url' => $formattedResponse['url'] ?? null,
                'detail' => $formattedResponse['detail'] ?? null,
            ]
        );
    }
}
