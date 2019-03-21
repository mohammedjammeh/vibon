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
            'id' => '12am4HWXKjuSTWeMBDnwac',
        	'name' => $name
        ];         
    }

    public function getPlaylist($id) 
    {
        return (object) [
            'id' => $id,
            'name' => 'Reggae Reggae Sound',
        ];  
    }

    public function addTracksToPlaylist($playlistId, $tracksId)
    {
        return (object) [
        	'id' => $playlistId,
            'tracks' => $tracksId
        ];     
    }

    public function getTrack($id)
    {
        return (object) [
            'id' => $id,
            'name' => 'Zion Train',
            'album' => (object) [
                'id' => '823n23923n3',
                'name' => 'Ghetto Youths',
                'images' => [
                    (object) [
                        'url' => 'ghettoyouth00.png'
                    ],
                    (object) [
                        'url' => 'ghettoyouth01.png'
                    ]
                ]
            ]
        ];  
    }
}
