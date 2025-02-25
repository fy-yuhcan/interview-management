<?php

namespace App\Livewire;

use App\Models\Event as ModelsEvent;
use Livewire\Component;

class Event extends Component
{
    public $events = [];

    public function mount()
    {
        $this->events = ModelsEvent::orderby('start_time', 'asc')->get();
    }


    public function render()
    {
        return view('livewire.event');
    }
}
