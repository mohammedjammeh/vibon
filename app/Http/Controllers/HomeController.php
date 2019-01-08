<?php

namespace App\Http\Controllers;
use App\Vibe;
use App\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::find(Auth::id());

        if ($user) {
            $vibes = $user->vibes()->where('user_id', Auth::id())->get();
        } else {
            $vibes = Vibe::all();
        }
        
        return view('home')->with('vibes', $vibes);
    }
}
