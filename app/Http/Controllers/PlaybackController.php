<?php

namespace App\Http\Controllers;

use App\MusicAPI\User;
use Illuminate\Http\Request;

class PlaybackController extends Controller
{
    public function __construct()
    {
        $this->middleware('setAccessToken');
    }

    public function userDevices()
    {
        return app(User::class)->devices();
    }
}
