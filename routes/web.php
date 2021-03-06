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
Whenever you update this, remember it also has to be updated in resources/js/core/vibes.js
*/

Auth::routes();

Route::get('/spotify', 'Auth\MusicAPI\CallbackController@spotifyAuth')->name('callback.spotify');
Route::get('/authorise', 'Auth\MusicAPI\AuthoriseController@authorise')->name('authorise');
Route::get('/welcome', 'Auth\MusicAPI\AuthoriseController@welcome')->name('welcome');

Route::middleware(['auth', 'check.user.tracks'])->group(function () {
    Route::get('/', 'HomeController@index')->name('index');
    Route::get('/home', 'HomeController@index')->name('home');
});

Route::middleware(['auth', 'check.user.tracks', 'only.ajax'])->group(function () {
    Route::get('/search/{input}', 'SearchController@search')->name('search');

    Route::resource('/vibe', 'VibeController');

    Route::get('/user/attributes', 'UserController@attributes')->name('user.attributes');

    Route::post('/join-request/vibe/{vibe}', 'JoinRequestController@store')->name('join-request.store');
    Route::delete('/join-request/delete/{joinRequest}', 'JoinRequestController@destroy')->name('join-request.destroy');
    Route::delete('/join-request/accept/{joinRequest}', 'JoinRequestController@accept')->name('join-request.accept');
    Route::delete('/join-request/reject/{joinRequest}', 'JoinRequestController@reject')->name('join-request.reject');

    Route::post('/user-vibe/vibe/{vibe}', 'UserVibeController@join')->name('user-vibe.join');
    Route::delete('/user-vibe/vibe/{vibe}', 'UserVibeController@leave')->name('user-vibe.leave');
    Route::delete('/user-vibe/vibe/{vibe}/user/{user}', 'UserVibeController@remove')->name('user-vibe.remove');

    Route::post('/track-vibe/vibe/{vibe}/track-api/{track}/category/{category}', 'TrackVibeController@store')->name('track-vibe.store');
    Route::delete('/track-vibe/vibe/{vibe}/track/{track}', 'TrackVibeController@destroy')->name('track-vibe.destroy');

    Route::post('/auto-vibe/{vibe}', 'AutoVibeController@refresh')->name('auto-vibe.refresh');

    Route::post('sync/vibe/{vibe}', 'VibeSynchronisationController@updatePlaylistTracks')->name('vibe-sync.vibe');
    Route::post('sync/playlist/{vibe}', 'VibeSynchronisationController@updateVibeTracks')->name('vibe-sync.playlist');

    Route::post('/vote/vibe/{vibe}/track/{track}', 'VoteController@store')->name('vote.store');
    Route::delete('/vote/vibe/{vibe}/track/{track}', 'VoteController@destroy')->name('vote.destroy');

    Route::post('/playback/broadcast', 'PlaybackController@broadcast')->name('playback.broadcast.vibe.track');

    Route::post('/pending-vibe-track-attach/vibe/{vibe}/track-api/{track}', 'PendingVibeTrack\AttachController@store')->name('pending-vibe-track-attach.store');
    Route::delete('/pending-vibe-track-attach/delete/{pendingVibeTrack}', 'PendingVibeTrack\AttachController@destroy')->name('pending-vibe-track-attach.destroy');
    Route::post('/pending-vibe-tracks-attach/respond/vibe/{vibe}', 'PendingVibeTrack\AttachController@respond')->name('pending-vibe-track-attach.respond');

    Route::post('/pending-vibe-track-detach/vibe/{vibe}/track/{track}', 'PendingVibeTrack\DetachController@store')->name('pending-vibe-track-detach.store');
    Route::delete('/pending-vibe-track-detach/delete/{pendingVibeTrack}', 'PendingVibeTrack\DetachController@destroy')->name('pending-vibe-track-detach.destroy');
    Route::post('/pending-vibe-tracks-detach/respond/vibe/{vibe}', 'PendingVibeTrack\DetachController@respond')->name('pending-vibe-track-detach.respond');
});
