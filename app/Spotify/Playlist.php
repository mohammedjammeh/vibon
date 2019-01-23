<?php

namespace App\Spotify;

use App\Vibe;
use SpotifyWebAPI\SpotifyWebAPI;
use Illuminate\Support\Facades\Session;

class Playlist

{





    public static function all() 

    {
        $user_id = Spotify::WebAPI()->me()->id;

        return Spotify::WebAPI()->getUserPlaylists($user_id);

    }





    public static function latest() 

    {

        return current(static::all()->items);

    }





    public static function create($title) 

    {

        return Spotify::WebAPI()->createPlaylist(['name' => $title]);

    }




    public static function update($id, $title)

    {

        return  Spotify::WebAPI()->updatePlaylist($id, ['name' => $title]);

    }




    public static function delete($id)

    {

        return  Spotify::WebAPI()->unfollowPlaylistForCurrentUser($id);

    }


}
