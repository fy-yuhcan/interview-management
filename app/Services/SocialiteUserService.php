<?php

namespace App\Services;

use App\Models\User;

class SocialiteUserService
{
    //userが存在するか確認し、存在しない場合は新規作成するサービスクラス
    public function findOrCreateUser($user,$provider)
    {
        return User::firstOrCreate(
            ['email' => $user->email],
            [
                'name' => $user->name,
                'provider' => $provider,
                'provider_id' => $user->id,
                'avatar' => $user->avatar,
                'token' => $user->token,
                'refresh_token' => $user->refreshToken,
                'expires_in' => $user->expiresIn,
                'token_created' => now()->timestamp,
                'token_type' => $user->tokenType,
            ]
            );
    }
}
