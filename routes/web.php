<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/redirect/{provider}', [SocialiteController::class, 'redirect']);
Route::get('/callback/{provider}', [SocialiteController::class, 'callback']);
