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

Route::get('/spotify', 'Auth\Music\CallbackController@spotifyAuth')->name('callback.spotify');
Route::get('/authorise', 'Auth\Music\AuthoriseController@authorise')->name('authorise');
Route::get('/welcome', 'Auth\Music\AuthoriseController@welcome')->name('welcome');

Route::get('/', 'HomeController@index')->name('index');
Route::get('/home', 'HomeController@index')->name('home');
Route::apiResource('/track', 'TrackController');
Route::resource('/vibe', 'VibeController');

Route::post('/join-request/vibe/{vibe}', 'JoinRequestController@store')->name('join-request.store');
Route::delete('/join-request/delete/{joinRequest}', 'JoinRequestController@destroy')->name('join-request.destroy');
Route::delete('/join-request/respond/{joinRequest}', 'JoinRequestController@respond')->name('join-request.respond');

Route::post('/user-vibe/vibe/{vibe}', 'UserVibeController@store')->name('user-vibe.store');
Route::delete('/user-vibe/vibe/{vibe}/user/{user}', 'UserVibeController@destroy')->name('user-vibe.destroy');

Route::post('/track-vibe/vibe/{vibe}', 'TrackVibeController@store')->name('track-vibe.store');
Route::delete('/track-vibe/vibe/{vibe}/track/{track}', 'TrackVibeController@destroy')->name('track-vibe.destroy');

Route::post('track-vibe-auto/vibe/{vibe}', 'TrackVibeAutoController@update')->name('track-vibe-auto.update');

