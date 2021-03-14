<?php

namespace App\Policies;

use App\PendingVibeTrack;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PendingVibeTrackPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can delete the pending vibe track.
     *
     * @param  \App\User  $user
     * @param  \App\PendingVibeTrack  $pendingVibeTrack
     * @return mixed
     */
    public function delete(User $user, PendingVibeTrack $pendingVibeTrack)
    {
        return (int) $user->id === (int) $pendingVibeTrack->user_id ||
             (int) $user->id === (int) $pendingVibeTrack->vibe->owner->id;
    }
}
