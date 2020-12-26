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
        $playlistTracks = collect($playlist->tracks->items)->pluck('track');

        $tracks = $vibe->showTracks;
        $pendingTracks = $vibe->pendingTracks->pluck('track');
        $tracksNotOnPlaylist = $this->tracksNotOnPlaylist($tracks, $playlistTracks);

        $loadedTracks = collect($this->load(
            $pendingTracks->concat($tracksNotOnPlaylist)
        ));

        return collect([
            'pending' => $loadedTracks->whereIn('id', $pendingTracks->pluck('api_id')),
            'on_playlist' => $this->getVibeTracksFromPlaylist($tracks, $playlistTracks),
            'not_on_playlist' => $loadedTracks->whereIn('id', $tracksNotOnPlaylist->pluck('api_id')),
            'not_on_vibon' => $playlistTracks->whereNotIn('id', $tracks->pluck('api_id'))
        ]);
    }

    public function load($tracks)
    {
        $tracksIDs = collect($tracks)->pluck('api_id')->toArray();
        return empty($tracksIDs) ? [] : $this->api->getTracks($tracksIDs)->tracks;
    }

    protected function getVibeTracksFromPlaylist($tracks, $playlistTracks)
    {
        $vibesPlaylistTracks = $tracks->map(function ($track) use($playlistTracks) {
            return $playlistTracks->where('id', $track->api_id)->first();
        });

        return $vibesPlaylistTracks->filter(function($value, $key) {
            return  $value !== null;
        });
    }

    protected function tracksNotOnPlaylist($tracks, $playlistTracks)
    {
        $playlistTracksIDs = $playlistTracks->pluck('id');
        return $tracks->whereNotIn('api_id', $playlistTracksIDs);
    }
}
