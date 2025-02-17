<?php

use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/login', 'auth.login')->name('login');
Route::get('/redirect/{provider}', [SocialiteController::class, 'redirect'])->name('redirect');
Route::get('/login/{provider}/callback', [SocialiteController::class, 'callback'])->name('callback');

Route::get('dev/google-import-test', function () {
    $user = \App\Models\User::find(1);
    $service = new \App\Services\GoogleCalendarService($user->token);
    $events = $service->getFormattedEvents();

    foreach ($events as $data) {
        (new \App\Services\EventCreateService())->createEvent($data, $user->id);
    }

    dd('Imported events: ', $events);
});
