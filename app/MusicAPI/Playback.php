<?php

namespace App\MusicAPI;

class Playback
{
    protected $api;

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
    }

    public function currentlyPlaying()
    {
        return $this->api->getPlaybackCurrentTrack();
    }
}
