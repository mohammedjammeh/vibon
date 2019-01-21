<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\User;
use App\Notifications\RequestToJoinAVibe;
use App\Notifications\ResponseToJoinAVibe;
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




    public function store(Vibe $vibe)

    {

        $vibe->users()->attach(Auth::id(), ['owner' => 0]);

		return redirect('/vibe/' . $vibe->id)->with('message', 'Welcome to the ' . $vibe->title . ' vibe.');

    }


    public function destroy(Vibe $vibe, User $user) 

    {

    	$vibe->users()->detach($user->id);
        
        return redirect()->back();
    }

}
