<?php // used in Vibe Listeners (created, updated) and trackAutoController

namespace App\AutoDJ;

use App\Track;
use App\Vibe;
use App\MusicAPI\Playlist;
use App\AutoDJ\Genre;

class Tracks
{
	public static function store($vibe)
	{
		$tracks = Track::belongsToMembersOf($vibe)->get();
		$tracksIDs = array_unique($tracks->pluck('id')->toArray());
		$vibe->tracks()->attach($tracksIDs, ['auto_related' => true]);
	}

	public static function storeAPI($vibe) 
	{
		$tracks = Genre::orderTracksByPopularity($vibe)->pluck('api_id')->toArray();
        app(Playlist::class)->addTracks($vibe, $tracks);
	}

	public static function update($vibe) 
	{
        $vibe->tracks()->wherePivot('auto_related', true)->detach();
		self::store($vibe);
	}

	public static function updateAPI($vibe)
	{
		$tracksIDs = $vibe->showTracks->pluck('api_id')->toArray();
		app(Playlist::class)->replaceTracks($vibe, $tracksIDs);
	}
}