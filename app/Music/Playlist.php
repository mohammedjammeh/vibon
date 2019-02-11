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


    // public function update($id, $name)
    // {
    //     return  $this->webAPI->updatePlaylist($id, ['name' => $name]);
    // }

    // public function delete($id)
    // {
    //     return  $this->webAPI->unfollowPlaylistForCurrentUser($id);
    // }

    // public function addTrack($playlistId, $trackId) 
    // {
    //     return $this->webAPI->addPlaylistTracks($playlistId, [$trackId]);
    // }

    // public function deleteTrack($playlistId, $trackId) 
    // {
    //     $playlist = $this->webAPI->getPlaylist($playlistId);
    //     $tracks = [
    //         'tracks' => [
    //             ['id' => $trackId],
    //         ],
    //     ];
    //     $this->webAPI->deletePlaylistTracks($playlistId, $tracks, $playlist->snapshot_id);
    // }
}
