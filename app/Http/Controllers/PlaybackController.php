<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\MusicAPI\Playback;

class PlaybackController extends Controller
{
    public function play(Vibe $vibe)
    {
        app(Playback::class)->play($vibe->api_id);
        return redirect($vibe->path);
    }

    public function pause()
    {
        app(Playback::class)->pause();
        return back();
    }

    public function previous()
    {
        app(Playback::class)->previous();
        return back();
    }

    public function next()
    {
        app(Playback::class)->next();
        return back();
    }
}
