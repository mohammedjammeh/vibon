<?php

namespace App\Http\Middleware\Music;

use Closure;
use App\Music\Spotify\WebAPI;

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

        new WebAPI();

        return $next($request);

    }

}
