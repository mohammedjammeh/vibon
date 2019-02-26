<?php

namespace App\Music;

use App\Music\InterfaceAPI;

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

    public function update($id, $name)
    {
        return $this->api->updatePlaylist($id, $name);
    }

    public function delete($id)
    {
        return $this->api->deletePlaylist($id);
    }

    public function addTrack($playlistId, $trackId) 
    {
        return $this->api->addTrackToPlaylist($playlistId, $trackId);
    }

    public function deleteTrack($playlistId, $trackId) 
    {
        return $this->api->deleteTrackFromPlaylist($playlistId, $trackId);
    }

    public function addNameAttribute($vibe) 
    {
        $playlist = $this->api->getPlaylist($vibe->api_id);
        $vibe->name = $playlist->name;
        return $vibe;
    }

    public function load($vibes) 
    {
        foreach ($vibes as $vibe) {
            $this->addNameAttribute($vibe);
        }
        return $vibes;
    }

    public function loadOne($vibe) 
    {
        $this->addNameAttribute($vibe);
        return $vibe;
    }
}
