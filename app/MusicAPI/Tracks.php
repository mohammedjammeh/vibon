<?php

namespace App\MusicAPI;

class Tracks
{
    protected $api;

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
    }

    public function getRecommendations($options)
    {
        return $this->api->getTrackRecommendations($options);
    }

    public function loadFor($vibe, $playlist)
    {
        $tracks = $vibe->showTracks;
        $playlistTracks = collect($playlist->tracks->items)->pluck('track');

        return $this->getTracksFromPlaylist($tracks, $playlistTracks)
            ->concat($this->getTracksNotOnPlaylist($tracks, $playlistTracks));
    }

    public function load($tracks)
    {
        $tracksIDs = collect($tracks)->pluck('api_id')->toArray();
        return $this->api->getTracks($tracksIDs)->tracks;
    }

    protected function getTracksFromPlaylist($tracks, $playlistTracks)
    {
        $tracksIDsForAPI = $tracks->pluck('api_id');
        return $playlistTracks->whereIn('id', $tracksIDsForAPI);
    }

    protected function getTracksNotOnPlaylist($tracks, $playlistTracks)
    {
        $playlistTracksIDs = $playlistTracks->pluck('id');
        $tracksNotOnPlaylist = $tracks->whereNotIn('api_id', $playlistTracksIDs);
        return $tracksNotOnPlaylist->isEmpty() ? [] : $this->load($tracksNotOnPlaylist);
    }
}
