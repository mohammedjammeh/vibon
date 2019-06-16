<?php

namespace App\Policies;

use App\User;
use App\JoinRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class JoinRequestPolicy
{
    use HandlesAuthorization;

    public function respond(User $user, JoinRequest $joinRequest)
    {
        return $joinRequest->vibe->owner->id == $user->id;
    }
}
