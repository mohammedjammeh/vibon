<?php

namespace App\Http\Controllers;

use App\MusicAPI\Playback;

class PlaybackController extends Controller
{
    public function currentlyPlaying()
    {
        return app(Playback::class)->currentlyPlaying();
    }
}
