<?php

namespace App\MusicAPI;

class Search
{
	protected $api;

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
    }  

    public function tracks($input)
    {
        return $this->api->search($input);
    }
}
