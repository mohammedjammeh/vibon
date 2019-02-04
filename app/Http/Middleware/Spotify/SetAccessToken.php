<?php

namespace App\Http\Middleware\Spotify;

use Closure;
use App\Spotify\WebAPI;

class SetAccessToken

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

        new WebAPI();

        return $next($request);

    }

}
