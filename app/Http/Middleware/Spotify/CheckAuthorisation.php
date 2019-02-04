<?php

namespace App\Http\Middleware\Spotify;

use Closure;
use App\Spotify\WebAPI;

class CheckAuthorisation

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
        $webAPI = new WebAPI();

        if(!$webAPI->userIsAuthorised()) {

            return $webAPI->authorise();

        }


        return $next($request);
    }

}
