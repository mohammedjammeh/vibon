<?php

namespace App\Http\Controllers\Auth\Music;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Music\Spotify\WebAPI as SpotifyWebAPI;
use App\Music\Apple\WebAPI as AppleWebAPI;

class AuthoriseController extends Controller
{
	public function __construct()
	{
		session(['authRequestMadeAt' => url()->previous()]);
	}

	public function spotify() 
	{
		$webAPI = new SpotifyWebAPI();
        return $webAPI->authorise();
	}

	public function apple() 
	{
		$webAPI = new AppleWebAPI();
        return $webAPI->authorise();
	}
}
