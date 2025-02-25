
@section('content')
<h1>イベント詳細ページ</h1>
@livewire('event-detail', ['event_id' => $event->id])

@endsection