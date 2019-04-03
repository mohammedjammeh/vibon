<?php

namespace App\Http\Middleware\Music;

use Closure;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Music\Playlist;
use App\AutoDJ\User as AutoUser;
use App\Music\Spotify\WebAPI as SpotifyWebAPI;
use App\Music\Spotify\WebAPI as AppleWebAPI;
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
        if(!Auth::check()) {
            return response(view('welcome'));
        }
        $this->shareUserDataWithAllViews();
        $this->checkAndUpateUserTracksForAutoVibes();
        if(Auth::user()->isAuthorisedWith(User::APPLE)) {
            new AppleWebAPI();
            return $next($request);
        }
        new SpotifyWebAPI();
        return $next($request);
    }

    public function checkAndUpateUserTracksForAutoVibes()
    {
        if(Auth::check()) {
            $timeUserTrackCreated = auth()->user()->tracks()->first()->pivot->created_at;
            $twentyFourHoursAgo = Carbon::now()->subDay();
            if ($twentyFourHoursAgo->greaterThanOrEqualTo($timeUserTrackCreated)) {
                AutoUser::updateTracks();
            } 
        }
    }

    public function shareUserDataWithAllViews()
    {
        View::composer('*', function($view){
            $user = Auth::user()->load('vibes.tracks');
            app(Playlist::class)->loadMany($user['vibes']);
            View::share('user', $user);
        });
    }
}
