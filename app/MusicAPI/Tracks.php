<?php

namespace App\MusicAPI;

use App\Track;
use Illuminate\Support\Arr;

class Tracks
{
    protected $api;

    protected $unloadedTracks;

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
        $this->unloadedTracks = collect([]);
    }

    public function load($tracks)
    {
        $tracksIDs = collect($tracks)->pluck('api_id')->toArray();
        return $this->api->getTracks($tracksIDs)->tracks;
    }

    public function loadFor($vibe, $playlist)
    {
        $tracks = $vibe->showTracks;
        $playlistTracks = collect($playlist->tracks->items)->pluck('track');
        $playlistTracksIDs = $playlistTracks->pluck('id')->toArray();
        $user = $this->userVibesTracks();
        $trackCollection = collect([]);

        foreach ($tracks as $track) {
            $loadedTrack = $this->getTrackAPI($track, $playlistTracks, $playlistTracksIDs);
            if($loadedTrack) {
                $loadedTrack = $this->updateTrackInfo($track, $loadedTrack, $vibe, $user);
                $trackCollection->push($loadedTrack);
            }
        }

        if ($this->unloadedTracks->isNotEmpty()) {
            $newLoadedTracks = $this->load($this->unloadedTracks);
            foreach ($newLoadedTracks as $loadedTrack) {
                $track = $tracks->where('api_id', $loadedTrack->id)->first();
                $loadedTrack = $this->updateTrackInfo($track, $loadedTrack, $vibe, $user);
                $trackCollection->push($loadedTrack);
            }
        }

        return $trackCollection;
    }

    public function getRecommendations($options)
    {
        return $this->api->getTrackRecommendations($options);
    }

    protected function getTrackAPI($track, $playlistTracks, $playlistTracksIDs)
    {
        $trackID = $track->api_id;
        if (in_array($trackID, $playlistTracksIDs)) {
            return $playlistTracks->where('id', $trackID)->first();
        }

        $this->unloadedTracks->push($track);
    }

    protected function updateTrackInfo($track, $loadedTrack, $vibe, $user)
    {
        $loadedTrack->votes_count = $track->votesCountOn($vibe);
        $loadedTrack->is_voted_by_user = $track->isVotedByAuthUserOn($vibe);
        $loadedTrack = $this->updateTrackVibonInfo($loadedTrack, $user);
        return $loadedTrack;
    }

    public function updateTracksVibonInfo($apiTracks)
    {
        $user = $this->userVibesTracks();
        return collect($apiTracks)->each(function ($apiTrack) use($user) {
            return $this->updateTrackVibonInfo($apiTrack, $user);
        });
    }

    protected function updateTrackVibonInfo($loadedTrack, $user)
    {
        $loadedTrack->vibon_id = $this->getTrackVibonID($loadedTrack->id);
        $loadedTrack->vibes = $this->getVibesIDs($loadedTrack->id, $user);
        return $loadedTrack;
    }

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
