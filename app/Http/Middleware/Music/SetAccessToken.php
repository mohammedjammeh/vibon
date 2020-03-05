<?php

namespace App\Http\Middleware\Music;

use Closure;
use App\AutoDJ\User as AutoUser;
use App\MusicAPI\User as UserAPI;
use Carbon\Carbon;

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
        if(!auth()->user()) {
            return redirect(route('welcome'));
        }

//        $this->refreshAccessToken();
//        $this->setAccessToken();
        $this->updateUserTracksForAutoVibes();
        return $next($request);
    }

//    protected function refreshAccessToken()
//    {
//        if($this->userAccessTokenHasBeenSetAnHourAgo()) {
//            app(UserAPI::Class)->refreshAcessToken(auth()->user()->access_token);
//            auth()->user()->update([
//                'access_token' => app('SpotifySession')->getAccessToken(),
//                'refresh_token' => app('SpotifySession')->getRefreshToken(),
//                'token_set_at' => date("Y-m-d H:i:s")
//            ]);
//        }
//    }
//
//    protected function userAccessTokenHasBeenSetAnHourAgo()
//    {
//        return Carbon::now()->subHour()->greaterThanOrEqualTo(auth()->user()->token_set_at);
//    }

//    protected function setAccessToken()
//    {
//        app(UserAPI::Class)->setAcessToken(auth()->user()->access_token);
//    }

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
