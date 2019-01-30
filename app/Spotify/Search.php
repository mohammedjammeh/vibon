<?php

namespace App\Spotify;

use App\Vibe;
use App\Track;
use App\Spotify\WebAPI;


class Search extends WebAPI

{

    public function tracks($track) 

    {

        return  $this->api->search($track, 'track')->tracks->items;

    }


    public function artists($artist)

    {
        return  $this->api->search($artist, 'artist')->artists->items;

    }

}
