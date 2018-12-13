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

            $client = new SpotifyWebApi;
            $session = new Session(
                config('services.spotify.client_id'),
                config('services.spotify.client_secret'),
                config('services.spotify.redirect')
            );

            $session->requestCredentialsToken();

            $accessToken = $session->getAccessToken();

            $client->setAccessToken($accessToken);

            return $client;

        });
    }
}
