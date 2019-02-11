<?php

namespace App\Http\Middleware\Music;

use Closure;
use App\User;
use App\Music\WebAPI;


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
        $user = User::findOrFail(Auth()->user()->id);

        if(!$user->isAuthorisedForAPI()) {

            $webAPI = new WebAPI();

            $webAPI->authorise();

        }


        return $next($request);
    }

}
