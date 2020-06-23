<?php

namespace App\Http\Controllers;

use App\Events\AutoVibeRefreshed;
use App\Traits\VibeShowTrait;
use App\Vibe;
use App\AutoDJ\Tracks as AutoTracks;
use App\MusicAPI\Playlist;

class TrackVibeAutoController extends Controller
{
    use VibeShowTrait;

    public function update(Vibe $vibe)
    {
		AutoTracks::update($vibe);
		AutoTracks::updateAPI($vibe);

		broadcast(new AutoVibeRefreshed($vibe))->toOthers();

        $loadedVibe = app(Playlist::class)->load($vibe);
        $message = $loadedVibe->name . ' has been refreshed.';
        return $this->showResponse($loadedVibe, $message);
    }
}
