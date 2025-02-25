<?php

namespace App\Livewire;

use App\Models\Event;
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


    public function render()
    {
        return view('livewire.event-detail')->layout('layouts.app'); 
    }
}
