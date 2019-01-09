<?php

namespace App\Http\Controllers;

use App\Vibe;
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
    	dd(request('vibe-id'));
    }

    public function destroy() {

    }
}
