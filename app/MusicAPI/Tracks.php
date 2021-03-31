<?php

namespace App\MusicAPI;

class Tracks
{
    protected $api;

    const PLAYLIST = 'playlist';
    const NOT_ON_PLAYLIST = 'not_on_playlist';
    const NOT_ON_VIBON = 'not_on_vibon';
    const PENDING_TO_ATTACH = 'pending_to_attach';
    const PENDING_TO_DETACH = 'pending_to_detach';

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
        $tracksNotOnPlaylist = $this->tracksNotOnPlaylist($tracks, $playlistTracks);
        $tracksToAttach = $vibe->pendingTracksToAttach->pluck('track');
        $tracksToDetach = $vibe->pendingTracksToDetach->pluck('track');

        $loadedTracks = collect($this->load(
            $tracksToAttach->concat($tracksToDetach)->concat($tracksNotOnPlaylist)->unique('id')
        ));

        return collect([
            self::PLAYLIST => $playlistTracks,
            self::NOT_ON_PLAYLIST => $loadedTracks->whereIn('id', $tracksNotOnPlaylist->pluck('api_id'))->flatten(),
            self::NOT_ON_VIBON => $playlistTracks->whereNotIn('id', $tracks->pluck('api_id'))->flatten(),
            self::PENDING_TO_ATTACH => $loadedTracks->whereIn('id', $tracksToAttach->pluck('api_id'))->flatten(),
            self::PENDING_TO_DETACH => $loadedTracks->whereIn('id', $tracksToDetach->pluck('api_id'))->flatten(),
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
