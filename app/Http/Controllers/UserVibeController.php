<?php

namespace App\Http\Controllers;

use App\Vibe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserVibeController extends Controller
{
	public function __construct()
    {
        $this->middleware('spotifySession');
        $this->middleware('auth');
        $this->middleware('spotifyAuth');
    }

    public function store(Request $request)
    {
		$request->validate([
			'key' => ['required', 'exists:vibes,key'],
		]);

		$vibe = Vibe::where('key', request('key'))->get();

    	if($vibe[0]->users()->where('user_id', '=' , Auth::id())->exists()) {
    		return redirect('home')->with('message', 'Sorry, you are already part of this vibe.');
    	}

    	$vibe[0]->users()->attach(Auth::id(), ['owner' => 0]);
		return redirect('/vibe/' . $vibe[0]->id)->with('message', 'Welcome to the ' . $vibe[0]->title . ' vibe.');
    }

    public function destroy() 
    {
    	
    }
}
