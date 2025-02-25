<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Exception;

class GoogleCalendarService
{
    protected $calendarService;

    /**
     * コンストラクタ
     *
     * @param string|GoogleCalendar $accessTokenOrCalendar 
     */
    public function __construct($accessTokenOrCalendar)
    {
        // モックで使えるようにするために、GoogleCalendarのインスタンスを受け取るように変更
        if ($accessTokenOrCalendar instanceof GoogleCalendar) {
            // 既にGoogleCalendarインスタンスが渡された場合はそのまま
            $this->calendarService = $accessTokenOrCalendar;
        } else {
            // アクセストークンをセットしてGoogleCalendarを生成
            $client = new GoogleClient();
            $client->setAccessToken($accessTokenOrCalendar);
            $this->calendarService = new GoogleCalendar($client);
        }
    }


    public function deleteEvent($calendarId,$googleCalendarId)
    {
        $this->calendarService->events->delete($calendarId, $googleCalendarId);
    }


    /**
     * ログイン中のユーザーが所有する primary カレンダーからイベントを取得し、フォーマットするメソッド
     *
     * @return array すべてのイベントオブジェクトの配列
     */
    public function getFormattedEvents(): array
    {
        $events = $this->getEvents();
        $formattedEvents = [];

        foreach ($events as $event) {
            $formattedEvents[] = $this->formatGoogleEvent($event);
        }

        return $formattedEvents;
    }

    /**
     * ログイン中のユーザーが所有する primary カレンダーからイベントを取得
     *
     * @return array すべてのイベントオブジェクトの配列
     */
    public function getEvents(): array
    {
        $optParams = [
            'maxResults'   => 100,
            'orderBy'      => 'startTime',
            'singleEvents' => true,
            //ここで現在時刻以降のイベントを取得するように設定
            'timeMin'      => date('c'), 
        ];

        $eventsResult = $this->calendarService->events->listEvents('primary', $optParams);
        $event = $eventsResult->getItems();

        return $event;
    }


    /**
     * GoogleイベントをEventCreateServiceで使えるようにフォーマットする
     */
    public function formatGoogleEvent($googleEvent): array
    {
        //googleカレンダーからのイベントの開始と終了時間を取得
        $start = $googleEvent->getStart();
        $end   = $googleEvent->getEnd();

        //整形するために時間を取得
        $startTime = $start->getDateTime() ?? $start->getDate();
        $endTime   = $end->getDateTime()   ?? $end->getDate();

        return [
            'calendar_id'       => $googleEvent->getId() ?? null,
            'title'             => $googleEvent->getSummary() ?? null,
            'start_time'        => $startTime,
            'end_time'          => $endTime,
            'reservation_time'  => null, 
            'status'            => '予定',
            'url'               => '', 
            'detail'            => $googleEvent->getDescription()   ?? null,
        ];
    }
}

