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

    public function mount($event_id)
    {
        $this->event_id = $event_id;
        $this->event = Event::find($event_id);
    }

    public function deleteEvent()
    {
        //googleカレンダーから削除し、DBからも削除
        $googleCalendarId = $this->event->calendar_id;

        $calendarId = 'primary';

        $accessToken = Auth::user()->token;

        $service = new GoogleCalendarService($accessToken);

        $service->deleteEvent($calendarId,$googleCalendarId);
    }


    public function render()
    {
        return view('livewire.event-detail')->layout('layouts.app'); 
    }
}
