<?php

namespace App\Services;

use App\Models\User;

class SocialiteUserService
{
    //userが存在するか確認し、存在しない場合は新規作成するサービスクラス
    public function findOrCreateUser($user,$provider)
    {
        return User::firstOrCreate(
            ['email' => $user->getEmail()],
            [
                'name' => $user->getName(),
                'provider' => $provider,
                'provider_id' => $user->getId(),
                'avatar' => $user->getAvatar(),
                'token' => $user->token,
            ]
            );
    }
}
