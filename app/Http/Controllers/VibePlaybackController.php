<?php

namespace App\Http\Controllers;

use App\Track;
use App\Vibe;

class VibePlaybackController extends Controller
{
    public function update(Vibe $vibe, Track $track)
    {
        $vibe->last_played_track_id = $track->id;
        $vibe->save();
        return 'Stored playing track.';
    }
}
