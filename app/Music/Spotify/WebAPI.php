<?php

namespace App\Music\Spotify;

use App\User;
use App\Vibe;
use App\Music\InterfaceAPI;
use SpotifyWebAPI\SpotifyWebAPI;
use Illuminate\Support\Facades\Auth;

class WebAPI implements InterfaceAPI
{
    public $api;
    public $user;

    public function __construct()
    {
        $this->api = new SpotifyWebAPI();

        if (Auth::check()) {
            $this->user = Auth::user();
            $this->setAuthorisedUserToken();
        } else {
            $this->setUnuthorisedUserToken();
        }
    }

    public function setAuthorisedUserToken() 
    {
        if(time() - strtotime($this->user->token_set_at) > 3599) {
            $this->refreshAuthorisedUserToken();
            $this->api->setAccessToken($this->user->access_token);
        } else {
            $this->api->setAccessToken($this->user->access_token);
        }
    }
    
    public function refreshAuthorisedUserToken()
    {
        app('Spotify')->refreshAccessToken($this->user->refresh_token);
        $this->user->access_token = app('Spotify')->getAccessToken();
        $this->user->refresh_token = app('Spotify')->getRefreshToken();
        $this->user->token_set_at = date("Y-m-d H:i:s");
        $this->user->save();
    }

    public function setUnuthorisedUserToken()
    {
        app('Spotify')->requestCredentialsToken();
        $credentialsToken = app('Spotify')->getAccessToken();
        $this->api->setAccessToken($credentialsToken);
    }

    public function options()
    {
        $options = [
            'scope' => [
                'playlist-modify-private',
                'playlist-modify',
                'playlist-read-private',
                'user-library-modify',
                'user-library-read',
                'user-read-email',
                'user-read-recently-played',
                'user-top-read',
            ],
        ];
        return $options;
    }

    public function authorise() 
    {
        return redirect(app('Spotify')->getAuthorizeUrl($this->options()))->send();
    }

    public function search($track)
    {
        return $this->api->search($track, 'track')->tracks->items;
    }
    
    public function createPlaylist($name)
    {
        return $this->api->createPlaylist(['name' => $name]);
    }

    public function updatePlaylist($id, $name) 
    {
        return $this->api->updatePlaylist($id, ['name' => $name]);
    }

    public function getPlaylist($id) 
    {
        return $this->api->getPlaylist($id);
    }

    public function deletePlaylist($id)
    {
        return $this->api->unfollowPlaylistForCurrentUser($id);
    }

    public function addTracksToPlaylist($playlistId, $tracksId)
    {
        return $this->api->addPlaylistTracks($playlistId, $tracksId);
    }

    public function deleteTrackFromPlaylist($playlistId, $trackId)
    {
        $playlist = $this->api->getPlaylist($playlistId);
        $tracks = [
            'tracks' => [
                ['id' => $trackId],
            ],
        ];
        $this->api->deletePlaylistTracks($playlistId, $tracks, $playlist->snapshot_id);
    }

    public function replaceTracksOnPlaylist($playlistId, $tracksId) 
    {
        $this->api->replacePlaylistTracks($playlistId, $tracksId);
    }

    public function getTrack($id)
    {
        return $this->api->getTrack($id);
    }

    public function getTrackRecommendations($options)
    {
        return $this->api->getRecommendations($options);
    }

    public function getUserRecentTracks()
    {
        return $this->api->getMyRecentTracks(['limit' => 50])->items;
    }

    public function getUserTopTracks()
    {
        return $this->api->getMyTop('tracks', ['limit' => 20])->items;
    }

    public function getArtist($id)
    {
        return $this->api->getArtist($id);
    }
}
