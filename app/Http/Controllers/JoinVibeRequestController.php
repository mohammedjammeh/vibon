<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\User;
use App\Notifications\RequestToJoinAVibe;
use App\Notifications\ResponseToJoinAVibe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JoinVibeRequestController extends Controller
{
	public function __construct()
    {
        $this->middleware('spotifySession');
        $this->middleware('auth');
        $this->middleware('spotifyAuth');
    }

    public function vibeOwner($vibe)
    {
    	return $vibe->users()->where('owner', 1)->first();
    }

    public function join(Vibe $vibe) 
    {   
        $this->vibeOwner($vibe)->notify(new RequestToJoinAVibe(Auth::id(), $vibe->id));
        return redirect('vibe/' . $vibe->id)->with('message', 'Your request has been sent.');
    }

    public function cancel(Vibe $vibe) 
    {
    	$this->vibeOwner($vibe)->notifications->where('data.requester_id', Auth::id())->where('data.vibe_id', $vibe->id)->first()->delete();
    	return redirect('vibe/' . $vibe->id)->with('message', 'Your request to join has been cancelled.');
    }

    public function leave(Vibe $vibe, User $user) 
    {
    	$this->vibeOwner($vibe)->notifications->where('data.requester_id', Auth::id())->where('data.vibe_id', $vibe->id)->first()->delete();
    	$vibe->users()->detach($user->id);
        return redirect()->back();
    }

    public function respond(Request $request, Vibe $vibe, User $user) 
    {
    	$notification = $this->vibeOwner($vibe)->unreadNotifications->where('data.requester_id', $user->id)->where('data.vibe_id', $vibe->id)->first();
    	$notification->markAsRead();

    	if($request->input('reject')) {
    		return redirect('/vibe/' . $vibe->id);
    	}

    	$notification->data = array('requester_id' => $user->id, 'vibe_id' => $vibe->id, 'accepted' => 1);
    	$notification->save();

    	$vibe->users()->attach($user->id, ['owner' => 0]);

    	$user->notify(new ResponseToJoinAVibe($vibe->id));

    	return redirect('/vibe/' . $vibe->id)->with('message', $user->name . ' is now part of this vibe.');
    }
}
