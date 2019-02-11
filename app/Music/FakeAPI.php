<?php

namespace App\Music;

use App\Vibe;
use App\Music\WebAPI;

use SpotifyWebAPI\SpotifyWebAPI;
use Illuminate\Support\Facades\Session;
use \stdClass;


class FakeAPI extends WebAPI

{

    public function userIsAuthorised()

    {

        return true;
    }





    public function bark()

    {

        return 'woof';
    }




    public function createPlaylist($name)

    {

        $playlist = new stdClass();

        $playlist->collaborative = false;

        $playlist->collaborative = false;

        $playlist->description = null;

        $playlist->external_urls = new stdClass();

        $playlist->followers = new stdClass();

        $playlist->href = 'https://api.spotify.com/v1/playlists/12am4HWXKjuSTWeMBDnwac';

        $playlist->id = '12am4HWXKjuSTWeMBDnwac';

        $playlist->images = new stdClass();

        $playlist->name = $name;

        $playlist->owner = new stdClass();

        $playlist->primary_color = null;

        $playlist->public = true;

        $playlist->snapshot_id = 'MSw5NTkyN2VmNTc5YzgxZTFjYmQwMGExZDU1NTJlODM5YmUyYWRlZDBj';

        $playlist->tracks = new stdClass();

        $playlist->type = 'playlist';

        $playlist->uri = 'spotify:user:itsyourboymo:playlist:12am4HWXKjuSTWeMBDnwac';


        return $playlist;
    	               
    }



    public function updatePlaylist()

    {

    	return 'yoo';
    	
    }



    public function unfollowPlaylistForCurrentUser($id)

    {

    	
    	
    }



    public function addPlaylistTracks($playlistId, $trackId) 

    {



    }



    public function getPlaylist($playlistId) 

    {



    }


    public function deletePlaylistTracks($playlistId, $tracks, $snapshotId)

    {



    }












    public function getTrack($trackId)


    {



    }










    public function search($track) 

    {



    }

}
