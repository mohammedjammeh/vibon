<?php

namespace App\MusicAPI;

use App\Track;
use Illuminate\Support\Arr;

class Tracks
{
    protected $api;

    protected $unloadedTracks = [];

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
    }

    public function getRecommendations($options)
    {
        return $this->api->getTrackRecommendations($options);
    }

    public function load($vibe, $playlist)
    {
        $tracks = $vibe->showTracks;
        $playlistTracks = collect($playlist->tracks->items)->pluck('track');
        $playlistTracksIDs = $playlistTracks->pluck('id')->toArray();
        $user = $this->userVibesTracks();

        $trackCollection = collect($tracks)->map(function ($track) use($playlistTracksIDs, $playlistTracks, $vibe, $user) {
            $loadedTrack = $this->getTrackAPI($track, $playlistTracks, $playlistTracksIDs);
            $loadedTrack = $this->checkAndUpdateTrackInfo($track, $loadedTrack, $vibe, $user);
            return $loadedTrack;
        });

        if (!empty($this->unloadedTracks)) {
            $newLoadedTracks = $this->loadUnloadedTracks();
            collect($newLoadedTracks)->each(function ($loadedTrack) use($vibe, $user, $trackCollection, $tracks) {
                $track = $tracks->where('id', $loadedTrack->id)->first();
                $loadedTrack = $this->checkAndUpdateTrackInfo($track, $loadedTrack, $vibe, $user);
                $trackCollection->push($loadedTrack);
            });
        }

        return $trackCollection;
    }

    public function getTrackAPI($track, $playlistTracks, $playlistTracksIDs)
    {
        $trackID = $track->api_id;
        if (in_array($trackID, $playlistTracksIDs)) {
            return $playlistTracks->where('id', $trackID)->first();
        }

        $this->unloadedTracks[] = $trackID;
    }

    public function checkAndUpdateTrackInfo($track, $loadedTrack, $vibe, $user)
    {
        $loadedTrack->votes_count = $track->votesCountOn($vibe);
        $loadedTrack->is_voted_by_user = $track->isVotedByAuthUserOn($vibe);

        $loadedTrack->vibon_id = $this->getTrackVibonID($loadedTrack->id);
        $loadedTrack->vibes = $this->getVibesIDs($loadedTrack->id, $user);

        return $loadedTrack;
    }

    public function loadUnloadedTracks()
    {
        return $this->api->getTracks($this->unloadedTracks);
    }

//    public function load($tracks)
//    {
//        $trackCollection = collect([]);
//        collect($tracks)->each(function ($track) use($trackCollection) {
//            $loadedTrack = $this->api->getTrack($track->api_id);
//            $trackCollection[] = $loadedTrack;
//        });
//        return $trackCollection;
//    }
//
//    public function check($apiTracks)
//    {
//        $user = $this->userVibesTracks();
//        return collect($apiTracks)->each(function ($apiTrack) use($user) {
//            $apiTrack->vibon_id = $this->getTrackVibonID($apiTrack->id);
//            $apiTrack->vibes = $this->getVibesIDs($apiTrack->id, $user);
//        });
//    }

    protected function userVibesTracks()
    {
        return auth()->user()->load(['vibes.tracks' => function($query) {
            $query->where('auto_related', false);
        }]);
    }

    protected function getTrackVibonID($apiTrackID)
    {
        $track = Track::where('api_id', $apiTrackID)->first();
        return !is_null($track) ? $track->id : null;
    }

    protected function getVibesIDs($apiTrackID, $user)
    {
        $vibesTracks = $user['vibes']->pluck('tracks', 'id')->toArray();
        $userTracksVibes = Arr::where($vibesTracks, function ($tracks, $vibeID) use($apiTrackID) {
            $tracksIDs = collect($tracks)->pluck('api_id');
            if ($tracksIDs->contains($apiTrackID)) {
                return $vibeID;
            }
            return null;
        });

        return array_keys(array_filter($userTracksVibes));
    }
}
