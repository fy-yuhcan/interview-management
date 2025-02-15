<!-- resources/views/auth/login.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>ログイン</h1>
        <!-- Livewire の OauthLogin コンポーネントを呼び出す -->
        @livewire('oauth-login')
    </div>
@endsection
