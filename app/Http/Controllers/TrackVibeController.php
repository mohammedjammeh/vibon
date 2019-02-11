<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\Track;

use App\Music\Playlist;
use App\Music\Spotify\WebAPI;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackVibeController extends Controller
{
    public function __construct() {
        parent::__construct();
        $this->middleware('checkAuthorisationForAPI');
    }

    public function store(WebAPI $webAPI, Track $track, Playlist $playlist, Vibe $vibe) 
    {
        $track = $track->find(request('track-api-id'));
    	if (!$track) {
            $track = Track::create(['api_id' => request('track-api-id')]);
    	} 
        $playlist->addTrack($vibe->api_id, $track->api_id);
        $track->vibes()->attach($vibe->id);
        return redirect()->back();
    }

    public function destroy(WebAPI $webAPI, Vibe $vibe, Track $track, Playlist $playlist) 
    {
        $vibe->tracks()->detach($track->id);
        $playlist->deleteTrack($vibe->api_id, $track->api_id);
        return redirect()->back();
    }
}
