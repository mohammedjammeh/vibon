<?php

use SpotifyWebAPI\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');


Route::get('/home', 'TrackController@index');





Route::get('/callback', function () {
    $session = new Session(
        config('services.spotify.client_id'),
        config('services.spotify.client_secret'),
        config('services.spotify.redirect')
    );




//    app('Spotify'); // this will use the Spotify service to do what is meant to be done by the code above

    $session->requestAccessToken($_GET['code']);
    $accessToken = $session->getAccessToken();


    dd($accessToken);
});