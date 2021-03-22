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
            'display_name' => 'Fake Stunner',
            'email' => 'faker@yahoo.com'
        ];
    }

    public function search($name)
    {
        return [
            $this->getTrack('search101'),
            $this->getTrack('search123'),
            $this->getTrack('search456'),
            $this->getTrack('search789')
        ];
    }

    public function createPlaylist($name, $description)
    {
        return (object) [
            'id' => '12am4HWXKjuSTWeMBDnwac',
        	'name' => $name,
            'description' => $description,
            'uri' => 'spotify:track:12am4HWXKjuSTWeMBDnwac',
            'tracks' => (object) [
                'items' => [
                    [
                        'track' => $this->getTrack('9826488')
                    ],
                    [
                        'track' => $this->getTrack('7609876')
                    ]
                ]
            ]
        ];         
    }

    public function getPlaylist($id)
    {
        return (object) [
            'id' => $id,
            'name' => 'Reggae Reggae Sound',
            'description' => 'Reggae Reggae Sound In Town',
            'uri' => 'spotify:track:12am4HWXKjuSTWeMBDnwac',
            'tracks' => (object) [
                'items' => [
                    [
                        'track' => $this->getTrack('9826488')
                    ],
                    [
                        'track' => $this->getTrack('7609876')
                    ]
                ]
            ]
        ];
    }

    public function updatePlaylist($id, $name, $description)
    {
        return (object) [
            'id' => $id,
            'name' => $name,
            'description' => $description
        ];     
    }

    public function deletePlaylist($id)
    {
        return (object) [];  
    }

    public function addTracksToPlaylist($playlistId, $tracksIds)
    {
        return (object) [
        	'id' => $playlistId,
            'tracks' => $tracksIds
        ];     
    }

    public function deleteTracksFromPlaylist($playlistId, $tracksIds)
    {

    }

    public function replaceTracksOnPlaylist($playlistId, $tracksIds)
    {
        return (object) [
            'id' => $playlistId,
            'tracks' => $tracksIds
        ];
    }

    public function reorderPlaylistTracks($playlistId, $rangeStart, $insertBefore)
    {

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
            'uri' => 'spotify:track:78aaIdueQSjI00fWUMAqna',
        ];  
    }

    public function getTracks($ids)
    {
        $tracks = [];
        foreach($ids as $id) {
            $tracks[] = $this->getTrack($id);
        }

        return (object) [
            'tracks' => $tracks
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
        $track01 = $this->getTrack('trackRecommendation01');
        $track01->name = 'track Recommendation 01';

        $track02 = $this->getTrack('trackRecommendation02');
        $track02->name = 'track Recommendation 02';

        $track03 = $this->getTrack('trackRecommendation03');
        $track03->name = 'track Recommendation 03';

        $track04 = $this->getTrack('trackRecommendation04');
        $track04->name = 'track Recommendation 04';

        return (object) [
            'tracks' => (object) [$track01, $track02, $track03, $track04]
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
