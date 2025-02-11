<?php

use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/login', 'auth.login')->name('login');
Route::get('/redirect/{provider}', [SocialiteController::class, 'redirect'])->name('redirect');
Route::get('/callback/{provider}', [SocialiteController::class, 'callback'])->name('callback');
