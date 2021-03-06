<?php

namespace App\MusicAPI;

class Artist
{
	protected $api;

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
    }  

    public function get($id) 
    {
        return $this->api->getArtist($id);
    }
}
