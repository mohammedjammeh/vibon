<?php

namespace App\Spotify;

use App\Vibe;
use App\Track;
use App\Spotify\WebAPI;


class Search

{

	protected $webAPI;

	

    public function __construct(WebAPI $webAPI)

    {

        $this->webAPI = $webAPI->api;

    }  




    public function tracks($track) 

    {

        return  $this->webAPI->search($track, 'track')->tracks->items;

    }




    public function artists($artist)

    {
        return  $this->webAPI->search($artist, 'artist')->artists->items;

    }

}
