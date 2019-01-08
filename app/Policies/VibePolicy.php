<?php

namespace App\Policies;

use App\User;
use App\Vibe;
use Illuminate\Auth\Access\HandlesAuthorization;

class VibePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the vibe.
     *
     * @param  \App\User  $user
     * @param  \App\Vibe  $vibe
     * @return mixed
     */
    public function update(User $user, Vibe $vibe)
    {
        $user_vibe = $user->vibes()->where('user_id', $user->id)->where('vibe_id', $vibe->id)->get();

        if (!$user_vibe->isEmpty()) {
            return $user_vibe[0]->pivot->user_id === $user->id && $user_vibe[0]->pivot->vibe_id === $vibe->id;
        }

    }

}
