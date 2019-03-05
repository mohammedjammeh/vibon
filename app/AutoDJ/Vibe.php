<?php 

namespace App\AutoDJ;

use App\Vibe as AppVibe;
use App\Music\Playlist;

class Vibe
{
	public function playlistAPI()
	{
		return app(Playlist::class);
	}

	public function addTracksToAPI($vibe, $type)
	{
		$tracks = $vibe->tracks()->where('auto_related', $type)->get();
		foreach ($tracks as $track) {
        	$this->playlistAPI()->addTrack($vibe->api_id, $track->api_id);
        }
	}

	public function removeTracksOnAPI($vibe)
	{
		foreach ($vibe->tracks as $track) {
			$this->playlistAPI()->deleteTrack($vibe->api_id, $track->api_id);
        }
	}

	public function storeTracks($vibe)
	{
        foreach ($vibe->users as $user) {
            foreach ($user->tracks as $track) {
            	$this->playlistAPI()->addTrack($vibe->api_id, $track->api_id);
                $vibe->tracks()->attach($track->id, ['auto_related' => 1]);
            }
        }
	}

	public function updateTracks($vibe) 
	{
		$this->removeTracksOnAPI($vibe);
		$vibe->tracks()->where('auto_related', 1)->detach();
		$this->storeTracks($vibe);
	}

	public function turnOnAutoForAPI($vibe)
	{
		$this->removeTracksOnAPI($vibe);
		$this->addTracksToAPI($vibe, 1);
	}

	public function turnOffAutoForAPI($vibe)
	{
		$this->removeTracksOnAPI($vibe);
		$this->addTracksToAPI($vibe, 0);
	}
}