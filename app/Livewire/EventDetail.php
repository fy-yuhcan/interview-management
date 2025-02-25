<?php

namespace App\Livewire;

use App\Models\Event;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EventDetail extends Component
{
    public $event;
    public $event_id;
    public $edit = false;
    public $title;
    public $start_time;
    public $end_time;
    public $reservation_time;
    public $detail;

    public function mount($event_id)
    {
        $this->event_id = $event_id;
        $this->event = Event::find($event_id);

        $this->title = $this->event->title;
        $this->start_time = $this->event->start_time;
        $this->end_time = $this->event->end_time;
        $this->reservation_time = $this->event->reservation_time;
        $this->detail = $this->event->detail;
    }

    //編集ボタンを押したとき
    public function editEvent()
    {
        $this->edit = true;
    }

    //キャンセルボタンを押したとき
    public function cancel()
    {
        $this->edit = false;
    }

    //更新ボタンを押したとき
    public function updateEvent()
    {
        //ここで更新とgoogleカレンダーの更新を行う
        $this->updateEventAndGoogleCalendar();

        $this->edit = false;

        session()->flash('message', 'イベントを更新しました');

        //再読み込み
        $this->mount($this->event_id);
    }

    public function updateEventAndGoogleCalendar()
    {
        $this->event->update([
            'title' => $this->title,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'reservation_time' => $this->reservation_time,
            'detail' => $this->detail,
        ]);

        //googleカレンダーの更新
        $user = Auth::user();
        $accessToken = $user->token;

        $service = new GoogleCalendarService($accessToken);

        //アクセストークンが期限切れかどうかをチェック
        $service->setAccessTokenForUser($user);

        $service->updateEvent($this->event);
    }

    public function deleteEvent()
    {
        //googleカレンダーから削除し、DBからも削除
        $googleCalendarId = $this->event->calendar_id;

        $calendarId = 'primary';

        $accessToken = Auth::user()->token;

        $service = new GoogleCalendarService($accessToken);

        //アクセストークンが期限切れかどうかをチェック
        $service->setAccessTokenForUser(Auth::user());

        $service->deleteEvent($calendarId,$googleCalendarId);

        //DBから削除
        $this->event->delete();

        session()->flash('message', 'イベントを削除しました');

        return redirect()->to('/events');
    }


    public function render()
    {
        return view('livewire.event-detail')->layout('layouts.app'); 
    }
}
