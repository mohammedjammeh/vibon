<?php

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


Auth::routes();

Route::get('/spotify', 'CallbackController@spotifyAuth');
Route::get('/apple', 'CallbackController@appleAuth');

Route::get('/spotify-login', 'Auth\Music\AuthoriseController@spotify')->name('auth.spotify');
Route::get('/apple-login', 'Auth\Music\AuthoriseController@apple')->name('auth.apple');

Route::resource('/', 'HomeController');
Route::resource('/home', 'HomeController');
Route::apiResource('/track', 'TrackController');
Route::resource('/vibe', 'VibeController');

Route::post('/join-request/vibe/{vibe}', 'JoinRequestController@store')->name('join-request.store');
Route::delete('/join-request/{joinRequest}/vibe/{vibe}', 'JoinRequestController@destroy')->name('join-request.destroy');
Route::patch('/join-request/{joinRequest}/vibe/{vibe}', 'JoinRequestController@respond')->name('join-request.respond');

Route::post('/user-vibe/vibe/{vibe}', 'UserVibeController@store')->name('user-vibe.store');
Route::delete('/user-vibe/vibe/{vibe}/user/{user}', 'UserVibeController@destroy')->name('user-vibe.destroy');

Route::post('/track-vibe/vibe/{vibe}', 'TrackVibeController@store')->name('track-vibe.store');
Route::delete('/track-vibe/vibe/{vibe}/track/{track}', 'TrackVibeController@destroy')->name('track-vibe.destroy');

