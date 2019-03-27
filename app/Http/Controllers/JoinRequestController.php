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
        $vibe->owner()->notify(new RequestToJoinAVibe(auth()->user()->id, $vibe->id));
        return redirect($vibe->path())->with('message', 'Your request has been sent.');
    }

    public function destroy(JoinRequest $joinRequest, Vibe $vibe) 
    {
        $joinRequest->delete();
        $vibe->ownerNotificationFrom(auth()->user()->id)->delete();
    	return redirect($vibe->path())->with('message', 'Your request to join has been cancelled.');
    }
    
    public function respond(Request $request, JoinRequest $joinRequest, Vibe $vibe) 
    {
        $joinRequester = $joinRequest->user;
        $joinRequest->delete();
        $vibe->ownerNotificationFrom($joinRequester->id)->markAsRead();
    	if($request->input('reject')) {
            $joinRequester->notify(new ResponseToJoinAVibe($vibe->id, 0));
    		return redirect($vibe->path());
    	}
        $joinRequester->notify(new ResponseToJoinAVibe($vibe->id, 1));
        $vibe->users()->attach($joinRequester->id, ['owner' => false]);
    	return redirect($vibe->path())->with('message', $joinRequester->name . ' is now part of this vibe.');
    }
}
