<?php

namespace App\Spotify;

use App\Vibe;
use App\Track;
use App\Spotify\WebAPI;


class Tracks extends WebAPI

{


    public function load($tracks) 

    {

        $apiTracks = [];


        foreach ($tracks as $track) {
            
            $apiTrack = $this->api->getTrack($track->api_id);

            $apiTracks[] = $apiTrack;

        }


        return $this->check($apiTracks);

    }







    public function check($apiTracks) 

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
