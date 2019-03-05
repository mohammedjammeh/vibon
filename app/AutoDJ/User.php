<?php 

namespace App\AutoDJ;

use App\Track;
use App\User as UserModel;
use App\Music\User as UserAPI;
use Illuminate\Support\Facades\Auth;

class User
{
	public function storeTracks()
	{
		$userAPI = app(UserAPI::class);
		foreach ($userAPI->recentTopAndOverallTopTracks() as $trackItem) {
        	$track = Track::firstOrCreate(['api_id' => $trackItem->id]);
        	$track->users()->attach(Auth::id(), ['type' => $trackItem->type]);
		}
	}

	public function updateTracks() 
	{
		Auth::user()->tracks()->detach();
		$this->storeTracks();
	}
}