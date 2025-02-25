<div>
    @if ($edit)
    <h2>イベント編集</h2>
    <form wire:submit.prevent="updateEvent">
        タイトル:
        <input type="text" wire:model="title"><br>

        開始日時:
        <input type="text" wire:model="start_time"><br>
        終了日時:
        <input type="text" wire:model="end_time"><br>

        リマインダー:
        <input type="text" wire:model="reservation_time"><br>
        詳細:
        <textarea wire:model="detail"></textarea><br>

        <button type="submit">更新</button>
        <button type="button" wire:click="cancelEdit">キャンセル</button>
    </form>
    @else
    <h2>イベント詳細</h2>
    <p><strong>タイトル:</strong> {{ $event->title }}</p>
    <p><strong>開始日時:</strong> {{ $event->start_time }}</p>
    <p><strong>終了日時:</strong> {{ $event->end_time }}</p>
    <p><strong>リマインダー:</strong> {{ $event->reservation_time }}</p>
    <p><strong>詳細:</strong> {{ $event->detail }}</p>

    <button wire:click="editEvent">編集する</button>
    <button wire:click="deleteEvent" onclick="confirm('本当に削除しますか？') || event.stopImmediatePropagation()">削除する</button>

    @if (session()->has('message'))
    <div style="color: green;">
        {{ session('message') }}
    </div>
    @endif
    @endif
</div>
