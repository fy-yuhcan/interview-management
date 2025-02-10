<?php

namespace App\Services;

use App\Models\User;

class SocialiteUserService
{
    //userが存在するか確認し、存在しない場合は新規作成するサービスクラス
    public function findOrCreateUser($providerUser,$provider)
    {
        return User::firstOrCreate(
            ['email' => $providerUser->getEmail()],
            [
                'name' => $providerUser->getName(),
                'provider' => $provider,
                'provider_id' => $providerUser->getId(),
                'avatar' => $providerUser->getAvatar(),
                'token' => $providerUser->token,
            ]
            );
    }
}
