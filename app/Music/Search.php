<?php

namespace App\Music;

use App\Music\InterfaceAPI;

class Search
{
	protected $api;

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
    }  

    public function tracks($track) 
    {
        return $this->api->search($track);
    }
}
