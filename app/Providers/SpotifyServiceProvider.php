<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SpotifyWebAPI\Session as SpotifySession;


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
            $spotifySession = new SpotifySession(
                config('services.spotify.client_id'),
                config('services.spotify.client_secret'),
                config('services.spotify.redirect')
            );

            return $spotifySession;
        });
    }
}
