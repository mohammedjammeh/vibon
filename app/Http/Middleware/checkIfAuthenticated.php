<?php

namespace App\Http\Middleware;

use Closure;
use App\AutoDJ\User as AutoUser;
use Carbon\Carbon;

class checkIfAuthenticated
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
            return redirect(route('welcome'));
        }

        $this->updateUserTracksForAutoVibes();
        return $next($request);
    }

    protected function updateUserTracksForAutoVibes()
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
