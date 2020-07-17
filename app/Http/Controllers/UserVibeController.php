<?php

namespace App\Http\Controllers;

use App\Events\UserJoinedVibe;
use App\Events\UserLeftVibe;
use App\Events\UserRemovedFromVibe;
use App\Traits\VibeShowTrait;
use App\Vibe;
use App\User;
use App\MusicAPI\Playlist;

class UserVibeController extends Controller
{
    use VibeShowTrait;

    public function join(Vibe $vibe)
    {
        $vibe->users()->attach(auth()->user()->id, ['owner' => false]);

        broadcast(new UserJoinedVibe(auth()->user(), $vibe))->toOthers();

        $loadedVibe = app(Playlist::class)->load($vibe);
        $message = 'Welcome to ' .  $loadedVibe->name . '.';
        return $this->showResponse($loadedVibe, $message);
    }

    public function leave(Vibe $vibe)
    {
        $user = auth()->user();
        $vibe->users()->detach($user->id);

        broadcast(new UserLeftVibe($user, $vibe))->toOthers();

        $loadedVibe = app(Playlist::class)->load($vibe);
        $message = 'You are no longer part of ' . $loadedVibe->name . '.';
        return $this->showResponse($loadedVibe, $message);
    }

    public function remove(Vibe $vibe, User $user)
    {
        $this->authorize('delete', $vibe);
    	$vibe->users()->detach($user->id);

        broadcast(new UserRemovedFromVibe($user, $vibe))->toOthers();

        $loadedVibe = app(Playlist::class)->load($vibe);
        $message = $user->display_name . ' is no longer a member of ' . $loadedVibe->name . '.';
        return $this->showResponse($loadedVibe, $message);
    }
}
