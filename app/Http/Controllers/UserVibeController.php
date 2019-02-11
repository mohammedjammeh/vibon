<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\User;
use App\Notifications\RemovedFromAVibe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserVibeController extends Controller
{
    public function store(Vibe $vibe)
    {
        $vibe->users()->attach(Auth::id(), ['owner' => 0]);
		return redirect('/vibe/' . $vibe->id)->with('message', 'Welcome to the ' . $vibe->title . ' vibe.');
    }

    public function destroy(Request $request, Vibe $vibe, User $user) 
    {
        if($request->input('vibe-member-remove')) {
            $user->notify(new RemovedFromAVibe($vibe->id));
        } 
    	$vibe->users()->detach($user->id);
        return redirect()->back();
    }
}
