<div>
    <h2>カレンダー</h2>

    <table border="1" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                <th>時間</th>
                @foreach($weekDates as $day)
                <th style="min-width: 150px;">
                    {{ $day->format('m/d (D)') }}
                </th>
                @endforeach
                
            </tr>
        </thead>
        <tbody>
            @foreach($dayHours as $hour)
            <tr>
                <td style="text-align: center;">
                    {{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00
                </td>
                @foreach($weekDates as $day)
                @php
                $dateKey = $day->format('Y-m-d');
                $hourKey = str_pad($hour, 2, '0', STR_PAD_LEFT);
            
                $hourEvents = $eventsDayHour[$dateKey][$hourKey] ?? [];
                @endphp
                <td style="vertical-align: top;">
                    @if(count($hourEvents) > 0)
                    @foreach($hourEvents as $event)
                    <a href="{{ route('events.show', ['event_id' => $event->id]) }}">
                        <strong>タイトル:</strong> {{ $event->title }}<br>
                        <strong>開始:</strong> {{ $event->start_time->format('H:i') }}
                    </a>
                    @endforeach
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>