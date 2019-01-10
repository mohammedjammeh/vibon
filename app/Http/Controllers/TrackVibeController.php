<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackVibeController extends Controller
{
	public function __construct()
    {
        $this->middleware('spotifySession');
        $this->middleware('auth');
        $this->middleware('spotifyAuth');
    }

    public function store(Request $request) {
    	$trackQuery = Track::where('api_id', request('api_id'))->get();

    	if ($trackQuery->isEmpty()) {
    		$track = Track::create(request(['api_id']));
    	} else {
    		$track = $trackQuery[0];
    	}

        $track->vibes()->attach(request('vibe-id'));
        return redirect('/home');
    }

    public function destroy() {

    }
}
