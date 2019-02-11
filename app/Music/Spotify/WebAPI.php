<?php

namespace App\Music\Spotify;

use App\User;
use App\Vibe;
use App\Music\InterfaceAPI;
use SpotifyWebAPI\SpotifyWebAPI;
use Illuminate\Support\Facades\Session;

class WebAPI implements InterfaceAPI
{
    public $api;
    public $user;

    public function __construct()
    {
        $this->api = new SpotifyWebAPI();
        $this->user = User::findOrFail(Auth()->user()->id);

        if ($this->user->isAuthorisedForAPI()) {
            $this->setAuthorisedUserToken();
        } else {
            $this->setUnuthorisedUserToken();
        }
    }

    public function setAuthorisedUserToken() 
    {
        if(time() - strtotime($this->user->api_token_set_at) > 3599) {
            $this->refreshAuthorisedUserToken();
            $this->api->setAccessToken($this->user->api_access_token);
        } else {
            $this->api->setAccessToken($this->user->api_access_token);
        }
    }

    public function refreshAuthorisedUserToken()
    {
        app('Spotify')->refreshAccessToken($this->user->api_refresh_token);
        $this->user->api_access_token = app('Spotify')->getAccessToken();
        $this->user->api_refresh_token = app('Spotify')->getRefreshToken();
        $this->user->api_token_set_at = date("Y-m-d H:i:s");
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

    public function getTrack($id)
    {
        return $this->api->getTrack($id);
    }
}
