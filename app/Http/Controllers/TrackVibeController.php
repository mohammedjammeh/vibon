<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackVibeController extends Controller
{
	public function __construct()
    {
        $this->middleware('spotifySession');
        $this->middleware('auth');
        $this->middleware('spotifyAuth');
    }

    public function store(Request $request) 
    {
    	$trackQuery = Track::where('api_id', request('track-api-id'))->get();

    	if ($trackQuery->isEmpty()) {
    		$track = Track::create([
                'api_id' => request('track-api-id')
            ]);
    	} else {
    		$track = $trackQuery[0];
    	}

        $this->spotifyAPI()->addPlaylistTracks(request('vibe-api-id'), [
            $track->api_id
        ]);

        $track->vibes()->attach(request('vibe-id'));
        return redirect()->back();
    }


    public function destroy(Vibe $vibe, Track $track) 
    {
        $vibe->tracks()->detach($track->id);
        $vibeOnSpotify = $this->spotifyAPI()->getPlaylist($vibe->api_id);

        $tracks = [
            'tracks' => [
                ['id' => $track->api_id],
            ],
        ];

        $this->spotifyAPI()->deletePlaylistTracks($vibe->api_id, $tracks, $vibeOnSpotify->snapshot_id);

        return redirect()->back();
    }
}
