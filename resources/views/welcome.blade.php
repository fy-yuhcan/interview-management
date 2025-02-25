@extends('layouts.app')

@section('content')
    <h1>イベント作成</h1>
    @livewire('input')
    <h1>イベント一覧</h1>
    @livewire('calendar')
@endsection