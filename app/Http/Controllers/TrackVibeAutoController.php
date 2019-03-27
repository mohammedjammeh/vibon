<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\AutoDJ\Tracks;

class TrackVibeAutoController extends Controller
{
    public function update(Vibe $vibe)
    {
		app(Tracks::class)->update($vibe);
		app(Tracks::class)->updateAPI($vibe);
		return redirect()->back();
    }
}
