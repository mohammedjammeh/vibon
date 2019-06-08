<?php

namespace App\Providers;

use App\Music\InterfaceAPI;
use App\Music\Spotify\WebAPI;
use Illuminate\Support\ServiceProvider;
use SpotifyWebAPI\Session;

class SpotifyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
    
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(InterfaceAPI::class, WebAPI::class);
        $this->app->singleton('SpotifySession', function () {
            $spotifySession = new Session(
                config('services.spotify.client_id'),
                config('services.spotify.client_secret'),
                config('services.spotify.redirect')
            );
            return $spotifySession;
        });
    }
}
