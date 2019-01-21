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

Route::get('/callback', 'CallbackController@spotifyAuth');




Route::resource('/', 'HomeController');

Route::resource('home', 'HomeController');

Route::apiResource('track', 'TrackController');

Route::resource('/vibe', 'VibeController');




Route::post('/join-request/{vibe}', 'JoinRequestController@join')->name('join-request.join');

Route::delete('/join-request/{vibe}', 'JoinRequestController@cancel')->name('join-request.cancel');

Route::delete('/join-request/{vibe}/user/{user}', 'JoinRequestController@leave')->name('join-request.leave');

Route::patch('/join-request/{vibe}/user/{user}', 'JoinRequestController@respond')->name('join-request.respond');





Route::post('/uservibe/{vibe}', 'UserVibeController@store')->name('uservibe.store');

Route::delete('uservibe/vibe/{vibe}/user/{user}', 'UserVibeController@destroy')->name('uservibe.destroy');




Route::post('/trackvibe', 'TrackVibeController@store')->name('trackvibe.store');

Route::delete('trackvibe/vibe/{vibe}/track/{track}', 'TrackVibeController@destroy')->name('trackvibe.destroy');

