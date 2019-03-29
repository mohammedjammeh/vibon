<?php 

namespace App\AutoDJ;

use App\Track;
use App\AutoDJ\Genre as AutoGenre;
use App\User as UserModel;
use App\Music\User as UserAPI;

class User
{
	public function storeTracks()
	{
		$userAPI = app(UserAPI::class);
		foreach ($userAPI->recentTopAndOverallTopTracks() as $trackItem) {
	        $track = Track::where('api_id', $trackItem->id)->first();
	        if (is_null($track)) {
	            $track = Track::create(['api_id' => $trackItem->id]);
	            AutoGenre::store($track);
	        }
        	$track->users()->attach(auth()->user()->id, ['type' => $trackItem->type]);
		}
	}

	public function updateTracks() 
	{
		auth()->user()->tracks()->detach();
		$this->storeTracks();
	}
}