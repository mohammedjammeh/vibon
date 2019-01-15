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

Route::post('/joinvibe/{vibe}', 'JoinVibeRequestController@join');
Route::delete('/joinvibe/{vibe}', 'JoinVibeRequestController@cancel');
Route::patch('/joinvibe/{vibe}/user/{user}', 'JoinVibeRequestController@respond');

Route::post('/uservibe/{vibe}', 'UserVibeController@store');
Route::delete('uservibe/vibe/{vibe}/user/{user}', 'UserVibeController@destroy');

Route::post('/trackvibe', 'TrackVibeController@store');
Route::delete('trackvibe/vibe/{vibe}/track/{track}', 'TrackVibeController@destroy');

