<?php

namespace App\Http\Middleware;

use Closure;

class SpotifyAuth
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

//        $options = [
//            'scope' => [
//                'playlist-read-private',
//                'user-read-private',
//            ],
//        ];
//
//        return redirect(app('Spotify')->getAuthorizeUrl($options));

        return $next($request);
    }
}
