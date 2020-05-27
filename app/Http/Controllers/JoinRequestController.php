<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\JoinRequest;
use App\Events\JoinRequestSent;
use App\Events\JoinRequestCancelled;
use App\Events\JoinRequestResponded;
use App\Traits\VibeShowTrait;
use App\MusicAPI\Playlist;

class JoinRequestController extends Controller
{
    use VibeShowTrait;

    public function __construct()
    {
        $this->middleware('authenticated');
    }

    public function store(Vibe $vibe) 
    {   
        $joinRequest = JoinRequest::create([
            'vibe_id' => $vibe->id,
            'user_id' => auth()->user()->id
        ]);
        event(new JoinRequestSent($joinRequest));

        $loadedVibe = app(Playlist::class)->load($joinRequest->vibe);
        $message = 'Your request to join ' . $loadedVibe->name . ' has been been sent.';
        return $this->showResponse($loadedVibe, $message);
    }

    public function destroy(JoinRequest $joinRequest) 
    {
        $joinRequest->delete();
        event(new JoinRequestCancelled($joinRequest));

        $loadedVibe = app(Playlist::class)->load($joinRequest->vibe);
        $message = 'Your request to join ' . $loadedVibe->name . ' has been been cancelled.';
        return $this->showResponse($loadedVibe, $message);
    }

    public function accept(JoinRequest $joinRequest)
    {
        $this->authorize('respond', $joinRequest);
        $joinRequest->delete();
        $joinRequest->vibe->users()->attach($joinRequest->user->id, ['owner' => false]);
        event(new JoinRequestResponded($joinRequest));

        $loadedVibe = app(Playlist::class)->load($joinRequest->vibe);
        $message = "You have accepted " . $joinRequest->user->username . "'s request to join " . $loadedVibe->name . ".";
        return $this->showResponse($loadedVibe, $message);
    }

    public function reject(JoinRequest $joinRequest)
    {
        $this->authorize('respond', $joinRequest);
        $joinRequest->delete();
        event(new JoinRequestResponded($joinRequest));

        $loadedVibe = app(Playlist::class)->load($joinRequest->vibe);
        $message = "You have rejected " . $joinRequest->user->username . "'s request to join " . $loadedVibe->name . ".";
        return $this->showResponse($loadedVibe, $message);
    }
}