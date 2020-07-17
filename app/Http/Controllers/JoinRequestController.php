<?php

namespace App\Http\Controllers;

use App\Events\JoinRequestAccepted;
use App\Events\JoinRequestRejected;
use App\Vibe;
use App\JoinRequest;
use App\Events\JoinRequestSent;
use App\Events\JoinRequestCancelled;
use App\Traits\VibeShowTrait;
use App\MusicAPI\Playlist;

class JoinRequestController extends Controller
{
    use VibeShowTrait;

    public function store(Vibe $vibe) 
    {   
        $joinRequest = JoinRequest::create([
            'vibe_id' => $vibe->id,
            'user_id' => auth()->user()->id
        ]);

        broadcast(new JoinRequestSent($joinRequest))->toOthers();

        $loadedVibe = app(Playlist::class)->load($joinRequest->vibe);
        $message = 'Your request to join ' . $loadedVibe->name . ' has been been sent.';
        return $this->showResponse($loadedVibe, $message);
    }

    public function destroy(JoinRequest $joinRequest)
    {
        broadcast(new JoinRequestCancelled($joinRequest))->toOthers();

        $joinRequest->delete();

        $loadedVibe = app(Playlist::class)->load($joinRequest->vibe);
        $message = 'Your request to join ' . $loadedVibe->name . ' has been been cancelled.';
        return $this->showResponse($loadedVibe, $message);
    }

    public function accept(JoinRequest $joinRequest)
    {
        $this->authorize('respond', $joinRequest);

        broadcast(new JoinRequestAccepted($joinRequest))->toOthers();

        $joinRequest->delete();
        $joinRequest->vibe->users()->attach($joinRequest->user->id, ['owner' => false]);

        $loadedVibe = app(Playlist::class)->load($joinRequest->vibe);
        $message = "You have accepted  {$joinRequest->user->display_name}'s request to join {$loadedVibe->name}.";
        return $this->showResponse($loadedVibe, $message);
    }

    public function reject(JoinRequest $joinRequest)
    {
        $this->authorize('respond', $joinRequest);

        broadcast(new JoinRequestRejected($joinRequest))->toOthers();

        $joinRequest->delete();

        $loadedVibe = app(Playlist::class)->load($joinRequest->vibe);
        $message = "You have rejected {$joinRequest->user->display_name}'s request to join {$loadedVibe->name}.";
        return $this->showResponse($loadedVibe, $message);
    }
}