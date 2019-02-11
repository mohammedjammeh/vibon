<?php

namespace App\Music;

use App\Music\WebAPI;


class Playlist

{

    protected $webAPI;



    public function __construct(WebAPI $webAPI)

    {

        $this->webAPI = $webAPI;

    }   




    public function create($name) 

    {

        return $this->webAPI->createPlaylist($name);

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
