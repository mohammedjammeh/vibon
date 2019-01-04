<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

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
        if (!Session::has('accessToken')) {
            $options = [
                'scope' => [
                    'playlist-modify-private',
                    'playlist-modify',
                    'playlist-read-private',
//                    'user-read-private',
                    'user-library-modify',
                    'user-library-read',
                    'user-read-email',
                ],
            ];

            return redirect(app('Spotify')->getAuthorizeUrl($options));
        }

        return $next($request);
    }
}
