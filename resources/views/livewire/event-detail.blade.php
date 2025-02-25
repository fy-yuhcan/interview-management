<div>
    <h2>イベント詳細</h2>
    <p><strong>タイトル:</strong> {{ $event->title }}</p>
    <p><strong>開始日時:</strong> {{ $event->start_time }}</p>
    <p><strong>終了日時:</strong> {{ $event->end_time }}</p>
    <p><strong>リマインダー:</strong> {{ $event->reservation_time }}</p>
    <p><strong>詳細:</strong> {{ $event->detail }}</p>

    <button wire:click="deleteEvent" onclick="confirm('本当に削除しますか？') || event.stopImmediatePropagation()">
        削除する
    </button>

    @if (session()->has('message'))
    <div style="color: green;">
        {{ session('message') }}
    </div>
    @endif

</div>
