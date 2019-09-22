<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\Track;
use App\MusicAPI\Playlist;
use App\AutoDJ\Genre as AutoGenre;

class TrackVibeController extends Controller
{
    public function __construct()
    {
        $this->middleware('setAccessToken');
    }

    public function store(Vibe $vibe)
    {
        $track = Track::where('api_id', request('track-api-id'))->first();
        if (is_null($track)) {
            $track = Track::create(['api_id' => request('track-api-id')]);
            AutoGenre::store($track);
        }
        $track->vibes()->attach($vibe->id, ['auto_related' => false]);
        $this->storeOnPlaylist($vibe, $track);
        return redirect()->back();
    }

    public function storeOnPlaylist($vibe, $track)
    {
        if (!$vibe->auto_dj) {
            return app(Playlist::class)->addTracks($vibe->api_id, [$track->api_id]);
        }
    }

    public function destroy(Vibe $vibe, Track $track) 
    {
        $vibe->tracks()->wherePivot('auto_related', '=', false)->detach();
        $this->destroyOnPlaylist($vibe, $track);
        return redirect()->back();
    }

    public function destroyOnPlaylist($vibe, $track)
    {
        if (!$vibe->auto_dj) {
            return app(Playlist::class)->deleteTrack($vibe->api_id, $track->api_id);
        }
    }
}
