<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\JoinRequest;
use App\Events\JoinRequestSent;
use App\Events\JoinRequestCancelled;
use App\Events\JoinRequestResponded;
use Illuminate\Http\Request;

class JoinRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkAuthorisationForAPI');
    }

    public function store(Vibe $vibe) 
    {   
        $joinRequest = JoinRequest::create([
            'vibe_id' => $vibe->id,
            'user_id' => auth()->user()->id
        ]);
        event(new JoinRequestSent($joinRequest));
        return redirect($vibe->path)->with('message', 'Your request has been sent.');
    }

    public function destroy(JoinRequest $joinRequest) 
    {
        $joinRequest->delete();
        event(new JoinRequestCancelled($joinRequest));
        $vibe = Vibe::find($joinRequest->vibe_id);
    	return redirect($vibe->path)->with('message', 'Your request to join has been cancelled.');
    }

    public function accept(JoinRequest $joinRequest)
    {
        $vibe = Vibe::find($joinRequest->vibe_id);
        $vibe->users()->attach($joinRequest->user->id, ['owner' => false]);
        event(new JoinRequestResponded($joinRequest));
        return redirect($vibe->path)->with('message', $joinRequest->user->name . ' is now part of this vibe.');
    }

    public function reject(JoinRequest $joinRequest)
    {
        event(new JoinRequestResponded($joinRequest));
        return redirect($vibe->path);
    }
    
    public function respond(Request $request, JoinRequest $joinRequest) 
    {
        $joinRequest->delete();
    	if($request->input('reject')) {
            return $this->reject($joinRequest);
    	}
        return $this->accept($joinRequest);
    }
}