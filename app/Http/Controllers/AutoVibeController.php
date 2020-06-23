<?php

namespace App\Http\Controllers;

use App\Events\AutoVibeRefreshed;
use App\Traits\VibeShowTrait;
use App\Vibe;
use App\AutoDJ\Tracks as AutoTracks;
use App\MusicAPI\Playlist;

class AutoVibeController extends Controller
{
    use VibeShowTrait;

    public function refresh(Vibe $vibe)
    {
		AutoTracks::update($vibe);
		AutoTracks::updateAPI($vibe);

        $loadedVibe = app(Playlist::class)->load($vibe);
        $message = $loadedVibe->name . ' has been refreshed.';

        broadcast(new AutoVibeRefreshed($vibe, $message))->toOthers();

        return $this->showResponse($loadedVibe, $message);
    }
}
