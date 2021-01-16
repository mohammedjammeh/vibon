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
        $tracksToAttach = $vibe->pendingTracksToAttach;
        $tracksToDetach = $vibe->pendingTracksToDetach;
        $tracksNotOnPlaylist = $this->tracksNotOnPlaylist($tracks, $playlistTracks);

        $loadedTracks = collect($this->load(
            $tracksToAttach->concat($tracksToDetach)->concat($tracksNotOnPlaylist)
        ));

        return collect([
            'playlist' => $playlistTracks,
            'not_on_playlist' => $loadedTracks->whereIn('id', $tracksNotOnPlaylist->pluck('api_id'))->flatten(),
            'not_on_vibon' => $playlistTracks->whereNotIn('id', $tracks->pluck('api_id'))->flatten(),
            'pending_to_attach' => $loadedTracks->whereIn('id', $tracksToAttach->pluck('api_id'))->flatten(),
            'pending_to_detach' => $loadedTracks->whereIn('id', $tracksToDetach->pluck('api_id'))->flatten(),
        ]);
    }

    public function load($tracks)
    {
        $tracksIDs = collect($tracks)->pluck('api_id')->toArray();
        return empty($tracksIDs) ? [] : $this->api->getTracks($tracksIDs)->tracks;
    }

    protected function tracksNotOnPlaylist($tracks, $playlistTracks)
    {
        $playlistTracksIDs = $playlistTracks->pluck('id');
        return $tracks->whereNotIn('api_id', $playlistTracksIDs);
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
}
