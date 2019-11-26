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

/*
Whenever you update this, remember it also has to be updated in resources/js/core/Vibes.js
*/

Auth::routes();

Route::get('/spotify', 'Auth\Music\CallbackController@spotifyAuth')->name('callback.spotify');
Route::get('/authorise', 'Auth\Music\AuthoriseController@authorise')->name('authorise');
Route::get('/welcome', 'Auth\Music\AuthoriseController@welcome')->name('welcome');

Route::get('/search', 'SearchController@search')->name('search');

Route::get('/', 'HomeController@index')->name('index');
Route::get('/home', 'HomeController@index')->name('home');
Route::apiResource('/track', 'TrackController');
Route::resource('/vibe', 'VibeController');

Route::post('/join-request/vibe/{vibe}', 'JoinRequestController@store')->name('join-request.store');
Route::delete('/join-request/delete/{joinRequest}', 'JoinRequestController@destroy')->name('join-request.destroy');
Route::delete('/join-request/accept/{joinRequest}', 'JoinRequestController@accept')->name('join-request.accept');
Route::delete('/join-request/reject/{joinRequest}', 'JoinRequestController@reject')->name('join-request.reject');

Route::post('/user-vibe/vibe/{vibe}', 'UserVibeController@join')->name('user-vibe.join');
Route::delete('/user-vibe/vibe/{vibe}/user/{user}', 'UserVibeController@remove')->name('user-vibe.remove');
Route::delete('/user-vibe/vibe/{vibe}', 'UserVibeController@leave')->name('user-vibe.leave');

Route::post('/track-vibe/vibe/{vibe}', 'TrackVibeController@store')->name('track-vibe.store');
Route::delete('/track-vibe/vibe/{vibe}/track/{track}', 'TrackVibeController@destroy')->name('track-vibe.destroy');

Route::post('/track-vibe-auto/vibe/{vibe}', 'TrackVibeAutoController@update')->name('track-vibe-auto.update');

Route::get('playback-user', 'PlaybackController@user')->name('playback-user');
Route::get('playback-user-devices', 'PlaybackController@userDevices')->name('playback-user-devices');

Route::post('sync/vibe/{vibe}', 'VibeSynchronisationController@updatePlaylistTracks')->name('vibe-sync.vibe');
Route::post('sync/playlist/{vibe}', 'VibeSynchronisationController@updateVibeTracks')->name('vibe-sync.playlist');

Route::post('/vote/vibe/{vibe}/track/{track}', 'VoteController@store')->name('vote.store');
Route::delete('/vote/vibe/{vibe}/track/{track}', 'VoteController@destroy')->name('vote.destroy');
