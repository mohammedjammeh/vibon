<?php

namespace App\Http\Controllers;

use App\MusicAPI\User as UserAPI;

class UserController extends Controller
{
    public function vibes()
    {
        $vibes = auth()->user()->load('vibes')->vibes;
        return [
            'auto' => $vibes->where('auto_dj', true)->pluck('id'),
            'manual' => $vibes->where('auto_dj', false)->pluck('id')
        ];
    }

    public function attributes()
    {
        app(UserAPI::class)->setAccessToken(auth()->user()->access_token);
        return auth()->user();
    }
}
