<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\MusicAPI\Playlist;

class UserComposer
{
    protected $user;

    public function __construct()
    {
        // this will probabaly have to be deleted as it is no longer used by the views
        // is transferred to UserController
        if (auth()->user()) {
            $this->user = auth()->user()->load('vibes.tracks');
            app(Playlist::class)->loadMany($this->user['vibes']);
        }
    }

    public function compose(View $view)
    {
        $view->with('user', $this->user);
    }
}