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
            'my_vibes' => $vibes->where('pivot.owner', true)->pluck('id'),
            'member_of_vibes' => $vibes->where('pivot.owner', false)->pluck('id'),
            'device_id' => optional(collect(app(UserAPI::class)->devices())->where('name', 'Vibon')->first())->id
        ];
    }
}
