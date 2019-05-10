<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\User;
use App\JoinRequest;

use App\Notifications\RequestToJoinAVibe;
use App\Notifications\ResponseToJoinAVibe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JoinRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkAuthorisationForAPI');
    }

    public function store(Vibe $vibe) 
    {   
        JoinRequest::create([
            'vibe_id' => $vibe->id,
            'user_id' => auth()->user()->id
        ]);
        $vibe->owner->notify(new RequestToJoinAVibe(auth()->user()->id, $vibe->id));
        return redirect($vibe->path)->with('message', 'Your request has been sent.');
    }

    public function destroy(JoinRequest $joinRequest) 
    {
        $vibe = Vibe::find($joinRequest->vibe_id);
        $vibe->owner->lastUnreadRequestNotificationFor($joinRequest)->delete();
        $joinRequest->delete();
    	return redirect($vibe->path)->with('message', 'Your request to join has been cancelled.');
    }
    
    public function respond(Request $request, JoinRequest $joinRequest) 
    {
        $vibe = Vibe::find($joinRequest->vibe_id);
        $vibe->owner->lastUnreadRequestNotificationFor($joinRequest)->markAsRead();

        $joinRequester = $joinRequest->user;
        $joinRequest->delete();

    	if($request->input('reject')) {
            $joinRequest->user->notify(new ResponseToJoinAVibe($vibe->id, false));
    		return redirect($vibe->path);
    	}
        $joinRequest->user->notify(new ResponseToJoinAVibe($vibe->id, true));
        $vibe->users()->attach($joinRequest->user->id, ['owner' => false]);

    	return redirect($vibe->path)->with('message', $joinRequest->user->name . ' is now part of this vibe.');
    }
}
