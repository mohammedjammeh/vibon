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
        // $this->app->bind(Food::class, function ($app) {
        //     return new Dinner();
        // });
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




interface Food
{
    function createRecipe();
}

class Breakfast implements Food
{
    function createRecipe()
    {
        return 'UberEats breakfast recipes';
    }
}

class Dinner implements Food
{
    function createRecipe()
    {
        return 'UberEats recipes';
    }
}

class FakeDinner extends Dinner
{
    function createRecipe()
    {
        return 'my fake stub for testing recipes creation ';
    }
}