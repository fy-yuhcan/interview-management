<?php

use App\Http\Controllers\SocialiteController;
use App\Livewire\EventDetail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/login', 'auth.login')->name('login');
Route::get('/redirect/{provider}', [SocialiteController::class, 'redirect'])->name('redirect');
Route::get('/login/{provider}/callback', [SocialiteController::class, 'callback'])->name('callback');
Route::get('/events/{event_id}', [EventDetail::class, 'show'])->name('events.show');