<?php

namespace App\Http\Controllers;

use App\Traits\VibeShowTrait;
use App\Vibe;
use App\AutoDJ\Tracks as AutoTracks;
use App\MusicAPI\Playlist;

class TrackVibeAutoController extends Controller
{
    use VibeShowTrait;

    public function __construct()
    {
        $this->middleware('setAccessToken');
    }

    public function update(Vibe $vibe)
    {
		AutoTracks::update($vibe);
		AutoTracks::updateAPI($vibe);

        $loadedVibe = app(Playlist::class)->load($vibe);
        $message = $loadedVibe->name . ' has been refreshed.';
        return $this->showResponse($loadedVibe, $message);
    }
}
