<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\User;
use App\Notifications\RequestToJoinAVibe;
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

    public function notify(Vibe $vibe) 
    {

        $vibeOwner = $vibe->users()->where('owner', 1)->first();
        $vibeOwner->notify(new RequestToJoinAVibe);

        return redirect('vibe/' . $vibe->id)->with('message', 'Your request has been sent.');

    }

    public function store(Request $request)
    {

        // $vibe[0]->users()->attach(Auth::id(), ['owner' => 0]);
		// return redirect('/vibe/' . $vibe[0]->id)->with('message', 'Welcome to the ' . $vibe[0]->title . ' vibe.');

        dd('store');
    }

    public function destroy(Vibe $vibe, User $user) 
    {
    	$vibe->users()->detach($user->id);
        return redirect()->back();

    }
}
