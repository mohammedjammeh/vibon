<?php

namespace App\Spotify;

use App\Vibe;
use App\Track;
use App\Spotify\WebAPI;


class Playlist

{

    protected $webAPI;




    public function __construct(WebAPI $webAPI)

    {

        $this->webAPI = $webAPI->api;

    }   




    public function create($title) 

    {

        return $this->webAPI->createPlaylist(['name' => $title]);

    }




    public function update($id, $title)

    {

        return  $this->webAPI->updatePlaylist($id, ['name' => $title]);

    }




    public function delete($id)

    {

        return  $this->webAPI->unfollowPlaylistForCurrentUser($id);

    }



    public function addTrack($playlistId, $trackId) 


    {

        return $this->webAPI->addPlaylistTracks($playlistId, [$trackId]);

    }




    public function deleteTrack($playlistId, $trackId) 

    {

        $playlist = $this->webAPI->getPlaylist($playlistId);

        $tracks = [
            'tracks' => [
                ['id' => $trackId],
            ],
        ];

        $this->webAPI->deletePlaylistTracks($playlistId, $tracks, $playlist->snapshot_id);

    }

}
