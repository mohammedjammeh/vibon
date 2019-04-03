<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\AutoDJ\Tracks as AutoTracks;

class TrackVibeAutoController extends Controller
{
    public function update(Vibe $vibe)
    {
		AutoTracks::update($vibe);
		AutoTracks::updateAPI($vibe);
		return redirect()->back();
    }
}
