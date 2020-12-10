<?php

namespace App\Traits;

use App\Policies\VibePolicy;

trait VibeShowTrait
{
    public function showResponse($loadedVibe, $message = '')
    {
        $this->updateAttributes($loadedVibe);
        return [
            'vibe' => $loadedVibe,
            'message' => $message
        ];
    }

    public function updateAttributes($loadedVibe)
    {
        $loadedVibe->destroyable = App(VibePolicy::class)->delete(auth()->user(), $loadedVibe);
        $loadedVibe->requests = $loadedVibe->joinRequests;
        $loadedVibe->currentUserIsAMember = $loadedVibe->hasMember(auth()->user());
        $loadedVibe->hasJoinRequestFromUser = $loadedVibe->hasJoinRequestFrom(auth()->user());
        $loadedVibe->joinRequestFromUser = $loadedVibe->joinRequestFrom(auth()->user());
        $loadedVibe->notifications = $loadedVibe->notifications();
    }
}