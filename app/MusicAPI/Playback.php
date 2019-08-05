<?php

namespace App\MusicAPI;

class Playback
{
    protected $api;

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
    }

    public function play($playlistId)
    {
        return $this->api->playPlayback($playlistId);
    }

    public function pause()
    {
        return $this->api->pausePlayback();
    }

    public function previous()
    {
        return $this->api->skipPlaybackToPreviousTrack();
    }

    public function next()
    {
        return $this->api->skipPlaybackToNextTrack();
    }
}
