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

    public function addTracks($vibe, $tracksIds)
    {
        $this->api->addTracksToPlaylist($vibe->api_id, $tracksIds);
    }

    public function deleteTracks($vibe, $tracksIds)
    {
        $this->api->deleteTracksFromPlaylist($vibe->api_id, $tracksIds);
    }

    public function replaceTracks($vibe, $tracksIds)
    {
        return $this->api->replaceTracksOnPlaylist($vibe->api_id, $tracksIds);
    }

    public function reorderTracks($vibe, $rangeStart, $insertBefore)
    {
        $this->api->reorderPlaylistTracks($vibe->api_id, $rangeStart, $insertBefore);
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
        $vibe->name = $this->htmlEntityDecode($playlist->name);
        $vibe->description = $this->htmlEntityDecode($playlist->description) ?? null;
        $vibe->uri = $playlist->uri;
        $vibe->api_tracks = app(Tracks::class)->loadFor($vibe, $playlist);
        $vibe->synced = $this->isSynced($vibe, $playlist);
        return $vibe;
    }

    public function isSynced($vibe, $playlist)
    {
        $vibeTracksIDs = $vibe->showTracks->pluck('api_id')->toArray();
        $playlistTracksIDs = collect($playlist->tracks->items)->pluck('track')->pluck('id')->toArray();
        return $vibeTracksIDs === $playlistTracksIDs ? true : false;
    }

    protected function htmlEntityDecode($string)
    {
        return html_entity_decode($string, ENT_QUOTES, 'UTF-8');
    }
}
