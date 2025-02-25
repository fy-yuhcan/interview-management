<?php

namespace App\Services;

use App\Models\Event;


class EventCreateService
{
    public function createEvent($formattedResponse, $userId,$createGoogleCalendarEvent)
    {
        //ここでgoogleカレンダーのid
        if (!isset($formattedResponse['calendar_id'])) {
            $formattedResponse['calendar_id'] = $createGoogleCalendarEvent->getId();
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
