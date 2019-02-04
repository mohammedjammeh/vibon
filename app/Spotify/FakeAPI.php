<?php

namespace App\Spotify;

use App\Vibe;
use SpotifyWebAPI\SpotifyWebAPI;
use Illuminate\Support\Facades\Session;

class FakeAPI extends WebAPI

{

    public $api;


    public function __construct()

    {
        $this->api->setAccessToken('authorised');
    }


    public function userIsAuthorised() 

    {
        return true;

    }

}
