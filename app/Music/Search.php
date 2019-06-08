<?php

namespace App\Music;

class Search
{
	protected $api;

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
    }  

    public function tracks($name) 
    {
        return $this->api->search($name);
    }
}
