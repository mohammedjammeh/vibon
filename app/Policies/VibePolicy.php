<?php

namespace App\Policies;

use App\User;
use App\Vibe;
use Illuminate\Auth\Access\HandlesAuthorization;

class VibePolicy
{
    use HandlesAuthorization;

    private function policyCheck($user, $vibe, $userVibe) 
    {
        if (!$userVibe->isEmpty()) {
            return $userVibe[0]->pivot->user_id === $user->id && $userVibe[0]->pivot->vibe_id === $vibe->id;
        }
    }

    /**
     * Determine whether the user can update the vibe.
     *
     * @param  \App\User  $user
     * @param  \App\Vibe  $vibe
     * @return mixed
     */
    public function update(User $user, Vibe $vibe)
    {
        $userVibe = $user->vibes()->where('user_id', $user->id)->where('vibe_id', $vibe->id)->get();
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
        $userVibe = $user->vibes()->where('user_id', $user->id)->where('vibe_id', $vibe->id)->where('vibe_dj', 1)->get();
        return $this->policyCheck($user, $vibe, $userVibe);
    }

}
