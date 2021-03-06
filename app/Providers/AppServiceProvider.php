<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\MusicAPI\InterfaceAPI;
use App\MusicAPI\Spotify\WebAPI;
use App\MusicAPI\Fake\WebAPI as FakeAPI;
use SpotifyWebAPI\Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(
            '*', 'App\Http\View\Composers\UserComposer'
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->bindMusicApiClass();
        $this->setSpotifySession();
    }

    protected function bindMusicApiClass()
    {
        if ($this->app->environment() === 'local') {
            $this->app->bind(InterfaceAPI::class, WebAPI::class);
        } else {
            app()->bind(InterfaceAPI::class, FakeAPI::class);
        }
    }

    protected function setSpotifySession()
    {
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
