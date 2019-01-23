<?php

namespace App\Spotify;

use App\Vibe;
use SpotifyWebAPI\SpotifyWebAPI;
use Illuminate\Support\Facades\Session;

class Spotify

{
    
    public static function WebAPI() 

    {

        $webAPI = new SpotifyWebAPI();

        if (!Session::has('accessToken')) {

            $webAPI->setAccessToken(Session::get('credentialsToken'));

        } else {

            $webAPI->setAccessToken(Session::get('accessToken'));

        }

        return $webAPI;

    }

}
