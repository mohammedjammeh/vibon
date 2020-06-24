<?php

namespace App\Policies;

use App\User;
use App\JoinRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class JoinRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user is an owner of a vibe
     *
     * @param User $user
     * @param JoinRequest $joinRequest
     * @return bool
     */
    public function respond(User $user, JoinRequest $joinRequest)
    {
        return $joinRequest->vibe->owner->id == $user->id;
    }
}
