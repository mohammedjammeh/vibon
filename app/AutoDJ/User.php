<?php // used in callbackController and SetAccessToken middleware

namespace App\AutoDJ;

use App\Track;
use App\AutoDJ\Genre as AutoGenre;
use App\Music\User as UserAPI;

class User
{
	public static function storeTracks()
	{
		$userAPI = app(UserAPI::class);
		$userAPI->recentTopAndOverallTopTracks()->each(function ($trackItem) {
		   	$track = Track::where('api_id', $trackItem->id)->first();
	        if (is_null($track)) {
	            $track = Track::create(['api_id' => $trackItem->id]);
	            AutoGenre::store($track);
	        }
        	$track->users()->attach(auth()->user()->id, ['type' => $trackItem->type]);
		});
	}

	public static function updateTracks() 
	{
		auth()->user()->tracks()->detach();
		self::storeTracks();
	}
}