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
        $this->middleware('setAccessToken');
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
    	return redirect($joinRequest->vibe->path)->with('message', 'Your request to join has been cancelled.');
    }

    public function respond(Request $request, JoinRequest $joinRequest)
    {
        $this->authorize('respond', $joinRequest);
        $joinRequest->delete();
        if($request->input('accept')) {
            return $this->accept($joinRequest);
        }
        return $this->reject($joinRequest);
    }

    public function accept(JoinRequest $joinRequest)
    {
        $joinRequest->vibe->users()->attach($joinRequest->user->id, ['owner' => false]);
        event(new JoinRequestResponded($joinRequest));
        return redirect($joinRequest->vibe->path)->with('message', $joinRequest->user->name . ' is now part of this vibe.');
    }

    public function reject(JoinRequest $joinRequest)
    {
        event(new JoinRequestResponded($joinRequest));
        return redirect($joinRequest->vibe->path);
    }
}