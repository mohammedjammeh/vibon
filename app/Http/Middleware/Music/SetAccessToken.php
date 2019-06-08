<?php

namespace App\Http\Middleware\Music;

use Closure;
use App\AutoDJ\User as AutoUser;
use App\Music\User as UserAPI;
use Carbon\Carbon;

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
        if(!auth()->user()) {
            return response(view('welcome'));
        }
        $this->checkAndUpateUserTracksForAutoVibes();
        app(UserAPI::Class)->setAccessToken();
        return $next($request);
    }

    public function checkAndUpateUserTracksForAutoVibes()
    {   
        if(auth()->user()->tracks()->first()) {
            $timeUserTrackCreated = auth()->user()->tracks()->first()->pivot->created_at;
            $twentyFourHoursAgo = Carbon::now()->subDay();
            if ($twentyFourHoursAgo->greaterThanOrEqualTo($timeUserTrackCreated)) {
                AutoUser::updateTracks();
            } 
        }
    }
}
