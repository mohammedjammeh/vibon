<?php

namespace App\Http\Controllers\Auth\Music;

use App\Http\Controllers\Controller;
use App\Music\User;

class AuthoriseController extends Controller
{
    public function authorise(User $user)
    {
        return $user->authorise();
    }

    public function welcome()
    {
        return view('welcome');
    }
}
