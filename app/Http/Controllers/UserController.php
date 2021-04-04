<?php

namespace App\Http\Controllers;

use App\MusicAPI\User as UserAPI;

class UserController extends Controller
{
    public function attributes()
    {
        app(UserAPI::class)->setAccessToken(auth()->user()->access_token);

        $user = auth()->user();
        $vibes = $user->load('vibes')->vibes;

        return [
            'id' => $user->id,
            'access_token' => $user->access_token,
            'token_set_at' => $user->token_set_at,
            'auto_vibes' => $vibes->where('auto_dj', true)->pluck('id'),
            'manual_vibes' => $vibes->where('auto_dj', false)->pluck('id'),
            'device_id' => optional(collect(app(UserAPI::class)->devices())->where('name', 'Vibon')->first())->id
        ];
    }
}
