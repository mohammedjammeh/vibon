<?php

namespace App\Traits;

use App\Policies\VibePolicy;
use App\MusicAPI\Playlist;

trait VibeShowTrait
{
//    public function loadAndUpdateAttributes($vibe)
//    {
//        $loadedVibe = app(Playlist::class)->load($vibe);
//        $this->updateAttributes($loadedVibe);
//        return $loadedVibe;
//    }

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
        $loadedVibe->updatable = App(VibePolicy::class)->update(auth()->user(), $loadedVibe);
        $loadedVibe->destroyable = App(VibePolicy::class)->delete(auth()->user(), $loadedVibe);
        $loadedVibe->requests = $loadedVibe->joinRequests;
        $loadedVibe->currentUserIsAMember = $loadedVibe->hasMember(auth()->user());
        $loadedVibe->hasJoinRequestFromUser = $loadedVibe->hasJoinRequestFrom(auth()->user());
        $loadedVibe->joinRequestFromUser = $loadedVibe->joinRequestFrom(auth()->user());
    }
}