<?php

namespace App\Spotify;

use App\Vibe;
use App\Spotify\WebAPI;
use SpotifyWebAPI\SpotifyWebAPI;
use Illuminate\Support\Facades\Session;

class PlaylistTracks

{


    public static function load(Vibe $vibe) 

    {

        $apiTracks = [];


        foreach ($vibe->tracks as $track) {
            
            $apiTrack = Spotify::WebAPI()->getTrack($track->api_id);

            $apiTracks[] = $apiTrack;

        }


        return static::check($apiTracks);

    }







    public static function check($apiTracks) 

    {

        foreach ($apiTracks as $apiTrack) {

            $user = auth()->user()->load('vibes.tracks');

            $apiTrack->vibes = array();


            foreach ($user['vibes'] as $userVibe) {

                foreach ($userVibe->tracks as $userVibeTrack) {
                    
                    if($apiTrack->id == $userVibeTrack->api_id) {

                        $apiTrack->vibes[] = $userVibe->id;

                        $apiTrack->vibon_id = $userVibeTrack->id;

                    }

                }

            }

        }

        return $apiTracks;

    }

}
