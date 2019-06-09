<?php

namespace App\MusicAPI\Fake;

use App\MusicAPI\Spotify\WebAPI as spotifyWebAPI;

class WebAPI extends spotifyWebAPI
{

    public function authorise()
    {
        return redirect('https://accounts.spotify.com/authorize?client_id=123');
    }

    public function getUser() {
        return (object) [
            'id' => '123iD',
            'email' => 'faker@yahoo.com'
        ];
    }

    public function search($name)
    {
        return [
            $this->getTrack('1'),
            $this->getTrack('2'),
            $this->getTrack('3'),
            $this->getTrack('4')
        ];
    }

    public function createPlaylist($name)
    {
        return (object) [
            'id' => '12am4HWXKjuSTWeMBDnwac',
        	'name' => $name
        ];         
    }

    public function updatePlaylist($id, $name) 
    {
        return (object) [
            'id' => $id,
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

    public function deletePlaylist($id)
    {
        return (object) [];  
    }

    public function addTracksToPlaylist($playlistId, $tracksId)
    {
        return (object) [
        	'id' => $playlistId,
            'tracks' => $tracksId
        ];     
    }

    public function replaceTracksOnPlaylist($playlistId, $tracksId) 
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
            ],
            'artists' => (object) [
                $this->getArtist('1'),
                $this->getArtist('2'),
                $this->getArtist('3'),
                $this->getArtist('4')
            ],
        ];  
    }

    public function getUserRecentTracks()
    {
        return [
            (object) [
                'track' => $this->getTrack('recent01')
            ],
            (object) [
                'track' => $this->getTrack('recent01')
            ],
            (object) [
                'track' => $this->getTrack('recent02')
            ],
            (object) [
                'track' => $this->getTrack('recent03')
            ],
            (object) [
                'track' => $this->getTrack('recent03')
            ],
            (object) [
                'track' => $this->getTrack('recent04')
            ]
        ];
    }

    public function getUserTopTracks()
    {
        return [
            $this->getTrack('top01'),
            $this->getTrack('top02'),
            $this->getTrack('top03'),
            $this->getTrack('top04')
        ];
    }

    public function getTrackRecommendations($options)
    {
        return (object) [
            'tracks' => (object) [
                $this->getTrack('trackRecommendation01'),
                $this->getTrack('trackRecommendation01'),
                $this->getTrack('trackRecommendation01'),
                $this->getTrack('trackRecommendation01')
            ]
        ];
    }

    public function getArtist($id) 
    {
        return (object) [
            'id' => $id,
            'genres' => array('roots', 'reggae')
        ];
    }
}
