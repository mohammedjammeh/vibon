<?php

namespace App\Http\Controllers;

use App\Traits\VibeShowTrait;
use App\Vibe;
use App\User;
use App\Notifications\RemovedFromAVibe;
use App\MusicAPI\Playlist;

class UserVibeController extends Controller
{
    use VibeShowTrait;

    public function __construct()
    {
        $this->middleware('setAccessToken');
    }

    public function join(Vibe $vibe)
    {
        $vibe->users()->attach(auth()->user()->id, ['owner' => false]);

        $loadedVibe = app(Playlist::class)->load($vibe);
        $message = 'Welcome to ' .  $loadedVibe->name . '.';
        return $this->showResponse($loadedVibe, $message);
    }

    public function leave(Vibe $vibe)
    {
        $user = auth()->user();
        $vibe->users()->detach($user->id);

        $loadedVibe = app(Playlist::class)->load($vibe);
        $message = 'You are no longer part of ' . $loadedVibe->name . '.';
        return $this->showResponse($loadedVibe, $message);
    }

    public function remove(Vibe $vibe, User $user)
    {
        $user->notify(new RemovedFromAVibe($vibe->id));
    	$vibe->users()->detach($user->id);

        $loadedVibe = app(Playlist::class)->load($vibe);
        $message = $user->username . ' is no longer a member of ' . $loadedVibe->name . '.';
        return $this->showResponse($loadedVibe, $message);
    }
}
