<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    //認証のリダイレクト処理
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    //認証のコールバック処理
    public function callback($provider)
    {
        $user = Socialite::driver($provider)->user();
        dd($user);
    }
}
