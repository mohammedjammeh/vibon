<?php

namespace App\Http\Controllers;

use App\Traits\VibeShowTrait;
use App\Vibe;
use App\Track;
use App\MusicAPI\Playlist;
use App\AutoDJ\Genre as AutoGenre;

class TrackVibeController extends Controller
{
    use VibeShowTrait;

    public function __construct()
    {
        $this->middleware('setAccessToken');
    }

    public function store(Vibe $vibe, $trackApiId)
    {
        $track = Track::where('api_id', $trackApiId)->first();
        if (is_null($track)) {
            $track = Track::create(['api_id' => $trackApiId]);
            AutoGenre::store($track);
        }
        $track->vibes()->attach($vibe->id, ['auto_related' => false]);
        $this->storeOnPlaylist($vibe, $track);

        $loadedVibe = app(Playlist::class)->load($vibe);
        return $this->showResponse($loadedVibe);
    }

    public function storeOnPlaylist($vibe, $track)
    {
        if (!$vibe->auto_dj) {
            return app(Playlist::class)->addTracks($vibe, [$track->api_id]);
        }
    }

    public function destroy(Vibe $vibe, Track $track) 
    {
        $vibe->tracks()
            ->wherePivot('auto_related', false)
            ->wherePivot('track_id', $track->id)
            ->detach();

        $this->destroyOnPlaylist($vibe, $track);

        $loadedVibe = app(Playlist::class)->load($vibe);
        return $this->showResponse($loadedVibe);
    }

    public function destroyOnPlaylist($vibe, $track)
    {
        if (!$vibe->auto_dj) {
            return app(Playlist::class)->deleteTrack($vibe, $track->api_id);
        }
    }
}
