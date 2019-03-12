<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\AutoDJ\Tracks;

class TrackVibeAutoController extends Controller
{
    public function update(Vibe $vibe)
    {
    	$autoTracks = app(Tracks::class);
		$autoTracks->update($vibe);
		$autoTracks->updateAPI($vibe);
		return redirect()->back();
    }
}
