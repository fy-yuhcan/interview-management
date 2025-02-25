<div>
    <h2>イベント一覧</h2>

    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>タイトル</th>
                <th>開始時間</th>
                <th>終了時間</th>
                <th>リマインダー</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($events as $event)
            <tr></tr>
                <td>{{ $event->title }}</td>
                <td>{{ $event->start_time }}</td>
                <td>{{ $event->end_time }}</td>
                <td>{{ $event->reservation_time }}</td>
                <td>{{ $event->detail }}</td>
            @endforeach($events as $event)
        </tbody>
    </table>
</div>
