<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('setAccessToken');
    }

    public function getAccessToken()
    {
        return auth()->user()->access_token;
    }
}
