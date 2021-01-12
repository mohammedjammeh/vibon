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
        $userVibe = $user->vibes()->where('user_id', $user->id)->where('vibe_id', $vibe->id)->first();
        return $this->policyCheck($user, $vibe, $userVibe);
    }

    /**
     * Determine whether the user can delete the vibe.
     *
     * @param  \App\User  $user
     * @param  \App\Vibe  $vibe
     * @return mixed
     */
    public function delete(User $user, Vibe $vibe)
    {
        $userVibe = $user->vibes()->where('user_id', $user->id)->where('vibe_id', $vibe->id)->where('owner', true)->first();
        return $this->policyCheck($user, $vibe, $userVibe);
    }


    protected function policyCheck($user, $vibe, $userVibe)
    {
        if (!$userVibe) {
            return false;
        }

        return (int) $userVibe->pivot->user_id === $user->id &&
            (int) $userVibe->pivot->vibe_id === $vibe->id;
    }
}
