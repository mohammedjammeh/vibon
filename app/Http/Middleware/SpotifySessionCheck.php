<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class SpotifySessionCheck
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
        if (!Session::has('accessToken')) {
            app('Spotify')->requestCredentialsToken();
            $credentialsToken = app('Spotify')->getAccessToken();
            Session::put('credentialsToken', $credentialsToken);
        }

        return $next($request);
    }
}
