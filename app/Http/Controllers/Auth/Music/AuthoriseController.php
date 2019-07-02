<?php

namespace App\Http\Controllers\Auth\Music;

use App\Http\Controllers\Controller;
use App\MusicAPI\User;

class AuthoriseController extends Controller
{
    public function authorise(User $user)
    {
        return $user->authorise();
    }

    public function welcome()
    {
        if(!auth()->user()) {
            return view('welcome');
        }
        return redirect(route('home'));
    }
}
