<?php

use App\Http\Controllers\SocialiteController;
use App\Livewire\EventDetail;
use Illuminate\Support\Facades\Route;

Route::get('/events', function () {
    return view('welcome');
})->name('events');

Route::view('/login', 'auth.login')->name('login');
Route::get('/redirect/{provider}', [SocialiteController::class, 'redirect'])->name('redirect');
Route::get('/login/{provider}/callback', [SocialiteController::class, 'callback'])->name('callback');
Route::get('/events/{event_id}', EventDetail::class)->name('events.show');
Route::get('/user/settings', function () {
    return view('user.settings');
})->middleware('auth')->name('user.settings');
