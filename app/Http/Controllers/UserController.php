<?php

namespace App\Http\Controllers;

use App\MusicAPI\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('authenticated');
    }

    public function vibes()
    {
        return auth()->user()->load('vibes')->vibes->where('auto_dj', false)->pluck('id');
    }

    public function attributes()
    {
        app(User::class)->setAccessToken(auth()->user()->access_token);
        return auth()->user();
    }
}
