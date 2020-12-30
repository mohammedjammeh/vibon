<?php

namespace App\Http\Controllers;

use App\Events\PlaybackUpdated;

class PlaybackController extends Controller
{
    public function broadcast() {
        broadcast(new PlaybackUpdated(
            request('vibe_id'),
            request('track_id'),
            request('is_track_paused'),
            request('type')
        ))->toOthers();
    }
}
