<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Music\Playlist;

class UserComposer
{
    protected $user;

    public function __construct()
    {
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