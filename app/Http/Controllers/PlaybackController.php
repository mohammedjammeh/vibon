<?php

namespace App\Http\Controllers;

use App\MusicAPI\Playback;

class PlaybackController extends Controller
{
    public function play($vibeUri, $trackUri)
    {
        app(Playback::class)->play($vibeUri, $trackUri);
    }

    public function resume()
    {
        app(Playback::class)->resume();
    }

    public function pause()
    {
        app(Playback::class)->pause();
    }

    public function previous()
    {
        app(Playback::class)->previous();
    }

    public function next()
    {
        app(Playback::class)->next();
    }

    public function currentlyPlaying()
    {
        return app(Playback::class)->currentlyPlaying();
    }
}
