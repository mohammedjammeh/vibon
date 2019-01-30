<?php

namespace App\Spotify;

use App\Vibe;
use App\Track;
use App\Spotify\WebAPI;
use Illuminate\Support\Facades\Session;


class Playlist extends WebAPI

{

    public function create($title) 

    {

        return $this->api->createPlaylist(['name' => $title]);

    }




    public function update($id, $title)

    {

        return  $this->api->updatePlaylist($id, ['name' => $title]);

    }




    public function delete($id)

    {

        return  $this->api->unfollowPlaylistForCurrentUser($id);

    }



    public function addTrack($playlistId, $trackId) 


    {

        return $this->api->addPlaylistTracks($playlistId, [$trackId]);

    }




    public function deleteTrack($playlistId, $trackId) 

    {

        $playlist = $this->api->getPlaylist($playlistId);

        $tracks = [
            'tracks' => [
                ['id' => $trackId],
            ],
        ];

        $this->api->deletePlaylistTracks($playlistId, $tracks, $playlist->snapshot_id);

    }

}
