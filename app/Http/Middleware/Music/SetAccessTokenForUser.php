<?php

namespace App\Http\Middleware\Music;

use Closure;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Music\Playlist;
use App\Music\Spotify\WebAPI as SpotifyWebAPI;
use App\Music\Spotify\WebAPI as AppleWebAPI;

class SetAccessTokenForUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::check()) {
            View::composer('*', function($view){
                $user = Auth::user()->load('vibes.tracks');
                app(Playlist::class)->load($user['vibes']);
                View::share('user', $user);
            });
            
            if(Auth::user()->isAuthorisedWith(User::APPLE)) {
                new AppleWebAPI();
                return $next($request);
            }
            new SpotifyWebAPI();
            return $next($request);
        }

        return response(view('welcome'));
    }
}
