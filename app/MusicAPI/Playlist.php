<?php

namespace App\MusicAPI;

class Playlist
{
    protected $api;

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
    }

    public function create($name) 
    {
        return $this->api->createPlaylist($name);
    }

    public function get($id)
    {
        return $this->api->getPlaylist($id);
    }

    public function update($id, $name)
    {
        return $this->api->updatePlaylist($id, $name);
    }

    public function delete($id)
    {
        return $this->api->deletePlaylist($id);
    }

    public function addTracks($playlistId, $tracksId) 
    {
        return $this->api->addTracksToPlaylist($playlistId, $tracksId);
    }

    public function deleteTrack($playlistId, $trackId) 
    {
        return $this->api->deleteTrackFromPlaylist($playlistId, $trackId);
    }

    public function replaceTracks($playlistId, $tracksId)
    {
        return $this->api->replaceTracksOnPlaylist($playlistId, $tracksId);
    }

    public function load($vibe) 
    {
        $playlist = $this->api->getPlaylist($vibe->api_id);
        $vibe->name = $playlist->name;
        $vibe->uri = $playlist->uri;
        $this->checkIfSynced($vibe, $playlist);
        return $vibe;
    }

    public function loadMany($vibes) 
    {
        foreach ($vibes as $vibe) {
            $this->load($vibe);
        }
        return $vibes;
    }

    public function checkIfSynced($vibe, $playlist)
    {
        $vibeTracksIDs = $vibe->showTracks->pluck('api_id')->toArray();
        $playlistTracksIDs = collect($playlist->tracks->items)->pluck('track')->pluck('id')->toArray();
        $vibe->synced = $vibeTracksIDs === $playlistTracksIDs ? true : false;
        return $vibe;
    }

    public function reorderTracks($id, $rangeStart, $insertBefore)
    {
        $this->api->reorderPlaylistTracks($id, $rangeStart, $insertBefore);
    }
}
