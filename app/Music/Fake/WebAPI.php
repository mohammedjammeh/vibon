<?php

namespace App\Music\Fake;

use App\Vibe;
use App\Music\Spotify\WebAPI as spotifyWebAPI;
use Illuminate\Support\Facades\Session;

class WebAPI extends spotifyWebAPI
{
    public function createPlaylist($name)
    {
        return (object) [
            'id' => '12am4HWXKjuSTWeMBDnwac'
        ];         
    }
}
