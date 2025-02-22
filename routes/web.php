<?php

use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/login', 'auth.login')->name('login');
Route::get('/redirect/{provider}', [SocialiteController::class, 'redirect'])->name('redirect');
Route::get('/login/{provider}/callback', [SocialiteController::class, 'callback'])->name('callback');
Route::get('/user/settings', function () {
    return view('user.settings');
})->middleware('auth')->name('user.settings');