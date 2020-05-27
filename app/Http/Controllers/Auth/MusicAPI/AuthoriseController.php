<?php

namespace App\Http\Controllers\Auth\MusicAPI;

use App\Http\Controllers\Controller;
use App\MusicAPI\User as UserAPI;

class AuthoriseController extends Controller
{
    public function authorise(UserAPI $user)
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
