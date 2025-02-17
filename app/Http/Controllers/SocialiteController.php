<?php

namespace App\Http\Controllers;

use App\Services\SocialiteUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    protected $socialiteUserService;
    public function __construct(SocialiteUserService $socialiteUserService)
    {
        $this->socialiteUserService = $socialiteUserService;
    }

    //認証のリダイレクト処理
    public function redirect($provider)
    {
        return Socialite::driver($provider)
            ->scopes(['https://www.googleapis.com/auth/calendar.events'])
            ->with(['access_type' => 'offline'])
            ->redirect();
    }

    //認証のコールバック処理
    public function callback($provider)
    {
        $user = Socialite::driver($provider)->user();

        //socialiteUserServiceクラスのインスタンスを生成してfindOrCreateUserメソッドを呼び出して登録
        $user = $this->socialiteUserService->findOrCreateUser($user, $provider);

        //ログイン処理
        Auth::login($user, true);

        return redirect('/');
    }
}
