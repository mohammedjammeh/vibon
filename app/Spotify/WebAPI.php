<?php

namespace App\Spotify;

use App\Vibe;
use SpotifyWebAPI\SpotifyWebAPI;
use Illuminate\Support\Facades\Session;

class WebAPI

{

    public $api;




    public function __construct()

    {

        $this->api = new SpotifyWebAPI();


        if ($this->userIsAuthorised()) {

            $this->api->setAccessToken(Session::get('accessToken'));

        } else {

            app('Spotify')->requestCredentialsToken();

            $credentialsToken = app('Spotify')->getAccessToken();

            $this->api->setAccessToken($credentialsToken);

        }
        
    }




    public function userIsAuthorised() 

    {

        if (Session::has('accessToken')) {
            
            return true;

        }


        return false;

    }




    public function options()

    {

        $options = [

            'scope' => [

                'playlist-modify-private',

                'playlist-modify',

                'playlist-read-private',

                'user-library-modify',

                'user-library-read',

                'user-read-email',
                
            ],

        ];


        return $options;

    }




    public function authorise() 

    {

        return redirect(app('Spotify')->getAuthorizeUrl($this->options()))->send();

    }


}
