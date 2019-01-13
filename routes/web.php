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

Route::get('/uservibe/{vibe}', 'UserVibeController@notify');
Route::post('/uservibe', 'UserVibeController@store');
Route::delete('uservibe/vibe/{vibe}/user/{user}', 'UserVibeController@destroy');

Route::post('/trackvibe', 'TrackVibeController@store');
Route::delete('trackvibe/vibe/{vibe}/track/{track}', 'TrackVibeController@destroy');

