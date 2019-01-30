<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\Track;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Spotify\Playlist;
use App\Spotify\WebAPI;


class TrackVibeController extends Controller

{


    public function store(WebAPI $webAPI, Track $track, Playlist $playlist, Vibe $vibe) 

    {

        if(!$webAPI->userIsAuthorised()) {

            return $webAPI->authorise();

        }


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
        if(!$webAPI->userIsAuthorised()) {

            return $webAPI->authorise();

        }


        $vibe->tracks()->detach($track->id);

        $playlist->deleteTrack($vibe->api_id, $track->api_id);

        return redirect()->back();

    }


}
