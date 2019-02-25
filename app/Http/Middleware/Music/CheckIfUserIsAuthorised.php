<?php

namespace App\Http\Middleware\Music;

use Closure;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Music\Spotify\WebAPI;


class CheckIfUserIsAuthorised
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
        // If more APIs get added, the response to an unauthenticated user shouldn't be to automatically authorise him but to ask him if he wants to be authorised with one of the APIs. 
        // This can be done with like a pop up. 
        // The user will then be authenticated with the chosen api by sending him to authController.
        if(!Auth::check()) {
            $webAPI = new WebAPI();
            $webAPI->authorise();
        }
        return $next($request);
    }

}
