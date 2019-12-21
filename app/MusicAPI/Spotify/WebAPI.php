<?php

namespace App\MusicAPI\Spotify;

use App\MusicAPI\InterfaceAPI;
use SpotifyWebAPI\SpotifyWebAPI;
use Carbon\Carbon;

class WebAPI implements InterfaceAPI
{
    public $api;
    public $accessToken;
    public $refreshToken;

    public function __construct()
    {
        $this->api = new SpotifyWebAPI();
        $this->setAuthenticatedUserAccessToken(auth()->user());
    }

    public function setAuthenticatedUserAccessToken($user)
    {
        if(Carbon::now()->subHour()->greaterThanOrEqualTo($user->token_set_at)) {
            $this->refreshUserAccessToken($user);
        }
        $this->api->setAccessToken($user->access_token);
    }

    public function refreshUserAccessToken($user)
    {
        app('SpotifySession')->refreshAccessToken($user->refresh_token);
        $user->access_token = app('SpotifySession')->getAccessToken();
        $user->refresh_token = app('SpotifySession')->getRefreshToken();
        $user->token_set_at = date("Y-m-d H:i:s");
        $user->save();
    }

    public function options()
    {
        $options = [
            'scope' => [
                'playlist-modify-private',
                'playlist-modify-public',
                'playlist-modify',
                'playlist-read-private',
                'user-library-modify',
                'user-library-read',
                'user-read-email',
                'user-read-recently-played',
                'user-top-read',
                'user-modify-playback-state',
                'user-read-currently-playing',
                'user-read-playback-state',
                'streaming',
                'user-read-private',
            ],
        ];
        return $options;
    }

    public function authorise() 
    {
        return redirect(app('SpotifySession')->getAuthorizeUrl($this->options()))->send();
    }

    public function getUser()
    {
        return $this->api->me();
    }

    public function search($name)
    {
        return $this->api->search($name, 'track')->tracks->items;
    }
    
    public function createPlaylist($name, $description)
    {
        return $this->api->createPlaylist([
            'name' => $name,
            'description' => $description
        ]);
    }

    public function updatePlaylist($id, $name, $description)
    {
        return $this->api->updatePlaylist($id, [
            'name' => $name,
            'description' => $description
        ]);
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

    public function reorderPlaylistTracks($playlistId, $rangeStart, $insertBefore)
    {
        $this->api->reorderPlaylistTracks($playlistId, [
            'range_start' => $rangeStart,
            'insert_before' => $insertBefore
        ]);
    }

    public function getTrack($id)
    {
        return $this->api->getTrack($id);
    }

    public function getTracks($ids)
    {
        return $this->api->getTracks($ids);
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

    public function getUserDevices()
    {
        return $this->api->getMyDevices()->devices;
    }

    public function playPlayback($playlistUri, $trackUri)
    {
        parse_str(file_get_contents('php://input'), $_PUT);
        $deviceId = $_PUT['device_id'];

        $this->api->play($deviceId, [
            'context_uri' => $playlistUri,
            'offset' => [
                'uri' => $trackUri
            ],
        ]);
    }

    public function resumePlayback()
    {
        parse_str(file_get_contents('php://input'), $_PUT);
        $deviceId = $_PUT['device_id'];
        $this->api->play($deviceId, []);
    }

    public function getPlaybackCurrentTrack()
    {
        return (array)$this->api->getMyCurrentPlaybackInfo();
    }
}
