<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $spotifyAPI;

    public function spotifyAPI() {
        $this->spotifyAPI = new SpotifyWebAPI();

        if (!Session::has('accessToken')) {
            $this->spotifyAPI->setAccessToken(Session::get('credentialsToken'));
        } else {
            $this->spotifyAPI->setAccessToken(Session::get('accessToken'));
        }

        return $this->spotifyAPI;
    }
}
