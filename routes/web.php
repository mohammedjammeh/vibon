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




Route::resource('/', 'HomeController');

Route::resource('/home', 'HomeController');

Route::apiResource('/track', 'TrackController');

Route::resource('/vibe', 'VibeController');




Route::post('/join-request/vibe/{vibe}', 'JoinRequestController@join')->name('join-request.join');

Route::delete('/join-request/vibe/{vibe}', 'JoinRequestController@cancel')->name('join-request.cancel');

Route::delete('/join-request/vibe/{vibe}/user/{user}', 'JoinRequestController@leave')->name('join-request.leave');

Route::patch('/join-request/vibe/{vibe}/user/{user}', 'JoinRequestController@respond')->name('join-request.respond');




Route::post('/user-vibe/vibe/{vibe}', 'UserVibeController@store')->name('user-vibe.store');

Route::delete('/user-vibe/vibe/{vibe}/user/{user}', 'UserVibeController@destroy')->name('user-vibe.destroy');




Route::post('/track-vibe/vibe/{vibe}', 'TrackVibeController@store')->name('track-vibe.store');

Route::delete('/track-vibe/vibe/{vibe}/track/{track}', 'TrackVibeController@destroy')->name('track-vibe.destroy');

