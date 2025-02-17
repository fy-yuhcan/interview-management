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
            'timeMin'      => date('c'), 
        ];

        $eventsResult = $this->calendarService->events->listEvents('primary', $optParams);
        $event = $eventsResult->getItems();

        return $event;
    }
}

