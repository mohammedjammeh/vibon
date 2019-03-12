<?php

namespace App\Music;

use App\Music\InterfaceAPI;
use App\Music\Artist;
use Illuminate\Support\Facades\Auth;

class Tracks
{
    protected $api;

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
    }  

    public function load($tracks) 
    {
        $apiTracks = [];
        foreach ($tracks as $track) {
            $apiTrack = $this->api->getTrack($track->api_id);
            $apiTracks[] = $apiTrack;
        }
        return $apiTracks;
    }

    public function check($apiTracks) 
    {
        $user = Auth::user()->load('vibes.tracks');
        foreach ($apiTracks as $apiTrack) {
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

    public function getRecommendations($options)
    {
        return $this->api->getTrackRecommendations($options);
    }
}
