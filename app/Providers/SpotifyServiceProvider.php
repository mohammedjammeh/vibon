<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;


class SpotifyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Spotify', function () {

            $session = new Session(
                config('services.spotify.client_id'),
                config('services.spotify.client_secret'),
                config('services.spotify.redirect')
            );


            $options = [
                'scope' => [
                    'playlist-read-private',
                    'user-read-private',
                ],
            ];

            header('Location: ' . $session->getAuthorizeUrl($options));


//            LINE 38 TO 43 HAS TO BE REMOVED ONCE THE LINE BELOW WORKS..

//            The Code below ensures whether the user's access token has to be set or not.. If it is, send him to set it.. If not, he is all good to go..

//            if (accessToken is not set) {
//                $options = [
//                    'scope' => [
//                        'playlist-read-private',
//                        'user-read-private',
//                    ],
//                ];
//
//                header('Location: ' . $session->getAuthorizeUrl($options));
//            }

        });
    }
}
