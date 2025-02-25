<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Event;

class Calendar extends Component
{
    public array $weekDates = [];

    public array $events = [];

    public array $dayHours = [];

    public array $eventsDayHour = [];

    public Carbon $calendarStart;

    public Carbon $calendarEnd;

    public function mount()
    {
        $this->calendarStart = Carbon::today()->startOfWeek()->subDay();
        $this->calendarEnd = Carbon::today()->endOfWeek();
        $this->events = Event::get()->all();
        $this->selectSchedules();
        $this->weekDates = $this->getWeekDates();
        $this->dayHours = range(0, 23);
    }

    public function selectSchedules()
    {
        $filteredEvents = Event::whereBetween('start_time', [$this->calendarStart, $this->calendarEnd])
            ->orderBy('start_time')
            ->get()
            ->map(function ($event) {
                $event->start_time = Carbon::parse($event->start_time);
                $event->end_time   = Carbon::parse($event->end_time);

                return $event;
            });

        //グルーピング用の配列初期化
        $this->eventsDayHour = [];

        //イベントを日付×時間単位に振り分ける
        foreach ($filteredEvents as $event) {
            $dayKey  = Carbon::parse($event->start_time)->format('Y-m-d');
            $hourKey = Carbon::parse($event->start_time)->format('H');

            if (!isset($this->eventsDayHour[$dayKey])) {
                $this->eventsDayHour[$dayKey] = [];
            }
            if (!isset($this->eventsDayHour[$dayKey][$hourKey])) {
                $this->eventsDayHour[$dayKey][$hourKey] = [];
            }

            $this->eventsDayHour[$dayKey][$hourKey][] = $event;
        }
    }

    public function getWeekDates()
    {
        $dates = [];
        $date = $this->calendarStart;
        while ($date->lte($this->calendarEnd)) {
            $dates[] = $date->copy();
            $date->addDay();
        }
        return $dates;
    }

    public function render()
    {
        return view('livewire.calendar');
    }
}
