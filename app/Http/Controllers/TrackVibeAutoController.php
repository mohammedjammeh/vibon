<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\AutoDJ\Vibe as VibeAuto;

class TrackVibeAutoController extends Controller
{
    public function update(Vibe $vibe)
    {
    	$vibeAuto = app(VibeAuto::class);
		$vibeAuto->updateTracks($vibe);
		return redirect()->back();
    }
}
