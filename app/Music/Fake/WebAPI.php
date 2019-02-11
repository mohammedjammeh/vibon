<?php

namespace App\Music\Fake;

use App\Vibe;
use App\Music\Spotify\WebAPI as spotifyWebAPI;
use Illuminate\Support\Facades\Session;

class WebAPI extends spotifyWebAPI
{
    public function userIsAuthorised()
    {
        return true;
    }

    public function search($track) 
    {
    }

    public function createPlaylist($name)
    {
        return (object) [
            'id' => '12am4HWXKjuSTWeMBDnwac'
        ];         
    }

    public function updatePlaylist()
    {
    }

    public function unfollowPlaylistForCurrentUser($id)
    {	
    }

    public function addPlaylistTracks($playlistId, $trackId) 
    {
    }

    public function getPlaylist($playlistId) 
    {
    }

    public function deletePlaylistTracks($playlistId, $tracks, $snapshotId)
    {
    }

    public function getTrack($trackId)
    {
    }
}
