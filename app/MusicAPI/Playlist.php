<?php

namespace App\MusicAPI;

class Playlist
{
    protected $api;

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
    }

    public function create($name, $description)
    {
        return $this->api->createPlaylist($name, $description);
    }

    public function get($id)
    {
        return $this->api->getPlaylist($id);
    }

    public function update($id, $name, $description)
    {
        return $this->api->updatePlaylist($id, $name, $description);
    }

    public function delete($id)
    {
        return $this->api->deletePlaylist($id);
    }

    public function addTracks($vibe, $tracksId)
    {
        app(User::class)->setAccessToken($vibe->owner->access_token);
        $this->api->addTracksToPlaylist($vibe->api_id, $tracksId);
        app(User::class)->setAccessToken(auth()->user()->access_token);
    }

    public function deleteTrack($vibe, $trackId)
    {
        app(User::class)->setAccessToken($vibe->owner->access_token);
        $this->api->deleteTrackFromPlaylist($vibe->api_id, $trackId);
        app(User::class)->setAccessToken(auth()->user()->access_token);
    }

    public function replaceTracks($vibe, $tracksIDs)
    {
        return $this->api->replaceTracksOnPlaylist($vibe->api_id, $tracksIDs);
    }

    public function reorderTracks($vibe, $rangeStart, $insertBefore)
    {
        app(User::class)->setAccessToken($vibe->owner->access_token);
        $this->api->reorderPlaylistTracks($vibe->api_id, $rangeStart, $insertBefore);
        app(User::class)->setAccessToken(auth()->user()->access_token);
    }

    public function loadMany($vibes)
    {
        foreach ($vibes as $vibe) {
            $this->load($vibe);
        }
        return $vibes;
    }

    public function load($vibe) 
    {
        $playlist = $this->api->getPlaylist($vibe->api_id);
        $vibe->name = $playlist->name;
        $vibe->uri = $playlist->uri;
        $vibe->description = $playlist->description ?? null;

        $vibe->api_tracks = app(Tracks::class)->loadFor($vibe, $playlist);

        $this->checkIfSynced($vibe, $playlist);
        return $vibe;
    }

    public function checkIfSynced($vibe, $playlist)
    {
        $vibeTracksIDs = $vibe->showTracks->pluck('api_id')->toArray();
        $playlistTracksIDs = collect($playlist->tracks->items)->pluck('track')->pluck('id')->toArray();
        $vibe->synced = $vibeTracksIDs === $playlistTracksIDs ? true : false;
        return $vibe;
    }
}
