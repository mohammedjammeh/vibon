<?php

namespace App\MusicAPI;

class Playback
{
    protected $api;

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
    }

    public function play($playlistUri, $trackUri)
    {
        return $this->api->playPlayback($playlistUri, $trackUri);
    }

    public function resume()
    {
        return $this->api->resumePlayback();
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

    public function currentlyPlaying()
    {
        return $this->api->getPlaybackCurrentTrack();
    }
}
