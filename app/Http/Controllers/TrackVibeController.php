<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\Genre;
use App\Track;
use App\Music\Tracks as TracksAPI;
use App\Music\Playlist;
use App\Music\Artist;
use App\AutoDJ\Genre as AutoGenre;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackVibeController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkAuthorisationForAPI');
    }

    public function storeOnPlaylist($vibe, $track) 
    {
        if (!$vibe->auto_dj) {
            return app(Playlist::class)->addTracks($vibe->api_id, [$track->api_id]);
        }
    }

    public function destroyOnPlaylist($vibe, $track)
    {
        if (!$vibe->auto_dj) {
            return app(Playlist::class)->deleteTrack($vibe->api_id, $track->api_id);
        }
    }

    public function store(Vibe $vibe, Track $track, TracksAPI $tracksAPI) 
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

    public function destroy(Vibe $vibe, Track $track) 
    {   
        $vibe->tracks()->detach($track->id);
        $this->destroyOnPlaylist($vibe, $track);
        return redirect()->back();
    }
}
