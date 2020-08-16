<?php

namespace App\Http\Controllers;

use App\Events\VibeDeleted;
use App\Traits\VibeShowTrait;
use App\Vibe;
use App\MusicAPI\Playlist;
use App\MusicAPI\Tracks as TracksAPI;
use App\Events\VibeCreated;
use App\Events\VibeUpdated;
use App\Http\Requests\StoreVibe;

class VibeController extends Controller
{
    use VibeShowTrait;

    public function index(Playlist $playlist)
    {
        return Vibe::all()->map(function($vibe) use($playlist) {
            $loadedVibe = app(Playlist::class)->load($vibe);
            return $this->showResponse($loadedVibe);
        });
    }

    public function store(StoreVibe $request, Playlist $playlist)
    {
        $newPlaylist = $playlist->create(
            $request->input('name'),
            $request->input('description')
        );

        $vibe = Vibe::create([
            'api_id' => $newPlaylist->id,
            'open' => $request->input('open'),
            'auto_dj' => $request->input('auto_dj')
        ]);
        $vibe->users()->attach(Auth()->user()->id, ['owner' => true]);

        broadcast(new VibeCreated($vibe))->toOthers();

        $loadedVibe = app(Playlist::class)->load($vibe);
        return $this->showResponse($loadedVibe);
    }

    public function show(Vibe $vibe)
    {
        $loadedVibe = app(Playlist::class)->load($vibe);
        return $this->showResponse($loadedVibe);
    }

    public function update(StoreVibe $request, Vibe $vibe, Playlist $playlist)
    {
        $this->authorize('delete', $vibe);
        $vibe->update(request(['open', 'auto_dj']));
        $playlist->update($vibe->api_id, $request->input('name'), $request->input('description'));

        $message = $request->input('name') . ' has been updated.';
        broadcast(new VibeUpdated($vibe, $message))->toOthers();

        $loadedVibe = app(Playlist::class)->load($vibe);
        return $this->showResponse($loadedVibe);
    }

    public function destroy(Vibe $vibe, Playlist $playlist)
    {
        $this->authorize('delete', $vibe);
        $message = $playlist->load($vibe)->name . ' has been deleted.';

        broadcast(new VibeDeleted($vibe, $message))->toOthers();

        $playlist->delete($vibe->api_id);
        $vibe->users()->detach();
        $vibe->tracks()->detach();
        $vibe->delete();

        return ['message' => $message];
    }
}
