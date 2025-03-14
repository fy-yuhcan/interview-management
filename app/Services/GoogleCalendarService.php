<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Exception;

class GoogleCalendarService
{
    protected $client;
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
            $this->client = new GoogleClient();
            $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
            $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $this->client->setAccessToken($accessTokenOrCalendar);

            $this->calendarService = new GoogleCalendar($this->client);
        }
    }

    /**
     * イベントを作成するメソッド
     *
     * @param array $event
     * @return \Google\Service\Calendar\Event 作成したイベントオブジェクト
     */
    public function createEvent($event)
    {
        //iso8601形式に変換
        $startIso = Carbon::parse($event['start_time'])->toIso8601String();
        $endIso   = Carbon::parse($event['end_time'])->toIso8601String();

        $googleEvent = new \Google\Service\Calendar\Event([
            'summary' => $event['title'] ?? '',
            'description' => $event['detail'] ?? '',
            'start' => [
                'dateTime' => $startIso,
                'timeZone' => 'Asia/Tokyo',
            ],
            'end' => [
                'dateTime' => $endIso,
                'timeZone' => 'Asia/Tokyo',
            ]
        ]);

        $createdGoogleEvent = $this->calendarService->events->insert('primary', $googleEvent);

        return $createdGoogleEvent;
    }

    /**
     * イベントを更新するメソッド
     *
     * @param object $event
     * @return \Google\Service\Calendar\Event 更新したイベントオブジェクト
     */
    public function updateEvent($event)
    {
        //iso8601形式に変換
        $startIso = Carbon::parse($event->start_time)->toIso8601String();
        $endIso   = Carbon::parse($event->end_time)->toIso8601String();

        $googleEvent = new \Google\Service\Calendar\Event([
            'summary' => $event->title ?? '',
            'description' => $event->detail ?? '',
            'start' => [
                'dateTime' => $startIso,
                'timeZone' => 'Asia/Tokyo',
            ],
            'end' => [
                'dateTime' => $endIso,
                'timeZone' => 'Asia/Tokyo',
            ]
        ]);

        $this->calendarService->events->update('primary', $event->calendar_id, $googleEvent);

        return $googleEvent;
    }

    /**
     * ユーザーのアクセストークンが期限切れかどうかをチェックするメソッド
     *
     * @param User $user
     * @return void
     */
    public function setAccessTokenForUser(User $user)
    {
        if ($this->client->isAccessTokenExpired()) {
            $newToken = $this->client->fetchAccessTokenWithRefreshToken($user->refresh_token);
            $user->update([
                'token' => $newToken['access_token'],
                'expires_in' => $newToken['expires_in'],
                'token_created' => now()->timestamp,
            ]);

            $this->client->setAccessToken($newToken);
        }
    }

    /**
     * イベントを作成するメソッド
     *
     * @param string $calendarId カレンダーID（primary)
     * @param string $googleCalendarId GoogleカレンダーID
     */
    public function deleteEvent($calendarId, $googleCalendarId)
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
        $nowTokyo = (new \DateTime('now', new \DateTimeZone('Asia/Tokyo')))
        ->format(\DateTime::RFC3339);

        $optParams = [
            'maxResults'   => 100,
            'orderBy'      => 'startTime',
            'singleEvents' => true,
            'timeMin'      => $nowTokyo,
            //ここで指定しても変わらず理由不明
            'timeZone'     => 'Asia/Tokyo',
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
        // googleカレンダーからのイベントの開始と終了時間を取得
        $start = $googleEvent->getStart();
        $end   = $googleEvent->getEnd();

        // 整形するために時間を取得
        $startTime = $start->getDateTime() ?? $start->getDate();
        $endTime   = $end->getDateTime()   ?? $end->getDate();

        //なぜかgoogleカレンダーからのデータ取得だけtimezoneがおかしくなってしまうので強制的に変換する
        $startTimeTokyo = null;
        if ($startTime) {
            $startTimeTokyo = (new \DateTime($startTime, new \DateTimeZone('UTC')))
            ->setTimezone(new \DateTimeZone('Asia/Tokyo'))
            ->format('Y-m-d H:i:s');    
        }

        $endTimeTokyo = null;
        if ($endTime) {
            $endTimeTokyo = (new \DateTime($endTime, new \DateTimeZone('UTC')))
            ->setTimezone(new \DateTimeZone('Asia/Tokyo'))
            ->format('Y-m-d H:i:s');
        }

        return [
            'calendar_id'       => $googleEvent->getId()          ?? null,
            'title'             => $googleEvent->getSummary()     ?? null,
            'start_time'        => $startTimeTokyo,
            'end_time'          => $endTimeTokyo,
            'reservation_time'  => null,
            'status'            => '予定',
            'url'               => '',
            'detail'            => $googleEvent->getDescription() ?? null,
        ];

    }
}
