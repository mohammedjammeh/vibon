<?php

namespace App\Music;

class Tracks
{
    protected $api;

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
    }  

    public function load($tracks) 
    {
        $trackCollection = collect([]);
        collect($tracks)->each(function ($track) use($trackCollection) {
            $trackCollection[] = $this->api->getTrack($track->api_id);
        });
        return $trackCollection;
    }

    public function check($apiTracks) 
    {
        $user = auth()->user()->load('vibes.tracks');
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
