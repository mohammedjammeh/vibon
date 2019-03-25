<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Event;

use App\Vibe;
use App\Music\InterfaceAPI;
use App\Music\Playlist;
use App\Music\Tracks;
use App\Music\Fake\WebAPI as FakeAPI;
use App\Events\VibeCreated;
use App\Events\VibeUpdated;

class VibeTest extends TestCase
{
	use WithFaker, RefreshDatabase;

	public function test_vibe_requires_a_name()
	{
		$vibe = factory(Vibe::class)->raw(['name' => '']);
		$this->post(route('vibe.store'))->assertSessionHasErrors('name');
	}

	public function test_vibe_requires_a_description() 
	{
		$vibe = factory(Vibe::class)->raw(['description' => '']);
		$this->post(route('vibe.store'))->assertSessionHasErrors('description');
	}

	public function test_vibe_can_be_created()
	{
		Event::fake();
		$playlist = app(Playlist::class)->create('Party');
		$attributes = factory(Vibe::class)->raw([
			'name' => $playlist->name, 
			'api_id' => $playlist->id
		]);
		$this->post(route('vibe.store'), $attributes)
			->assertRedirect(Vibe::first()->path());
		$this->assertDatabaseHas('vibes', [
			'api_id' => $attributes['api_id'],
			'description' => $attributes['description']
		]);
	}

	public function test_vibe_created_event_is_triggered_when_a_vibe_is_created()
	{
		Event::fake();
		$playlist = app(Playlist::class)->create('Party');
		$attributes = factory(Vibe::class)->raw([
			'name' => $playlist->name, 
			'api_id' => $playlist->id
		]);
		$this->post(route('vibe.store'), $attributes);
		Event::assertDispatched(VibeCreated::class);
	}

	public function test_vibe_created_listener_ensures_that_new_vibe_has_auto_related_tracks_based_on_its_users_tracks()
	{
		$vibe = factory(Vibe::class)->create(['auto_dj' => false]);
		event(new VibeCreated($vibe));
		$vibeTracks = $vibe->tracks->pluck('api_id')->toArray();
		$vibeUserTrack = $vibe->users->first()->tracks->first()->api_id;
		$this->assertContains($vibeUserTrack, $vibeTracks);
	}

	public function test_vibe_show_view_gets_required_data()
	{
		$vibe = factory(Vibe::class)->create();
		$tracks = $vibe->showTracks();
		$loadedTracks = app(Tracks::class)->load($tracks);
		$this->get($vibe->path())->assertViewHasAll([
			'vibe' => app(Playlist::class)->load($vibe),
			'apiTracks' => app(Tracks::class)->check($loadedTracks)
		]);
	}

	public function test_vibe_can_be_viewed_by_a_user()
	{
		$vibe = factory(Vibe::class)->create();
		$this->get($vibe->path())
			->assertSuccessful()
			->assertSee($vibe->description);
	}

	public function test_vibe_is_shown_with_the_right_view() 
	{
		$vibe = factory(Vibe::class)->create();
		$this->get($vibe->path())->assertViewIs('vibe.show');
	}

	public function test_vibe_edit_page_cannot_be_accessed_by_a_non_member()
	{
		$this->withoutExceptionHandling();
		$this->expectException(AuthorizationException::class);
		$vibe = factory(Vibe::class)->create();
		$this->get(route('vibe.edit', [$vibe]));
	}

	public function test_vibe_cannot_be_updated_by_a_non_member()
	{
		$vibe = factory(Vibe::class)->create();
		$this->patch(route('vibe.update', $vibe), [
			'name' => 'Shaka Dance',
            'description' => 'Shakala Boom Boom',
         	'open' => $vibe->open,
            'auto_dj' =>  $vibe->auto_dj
        ]);
		$this->assertDatabaseMissing('vibes', [
			'id' => $vibe->id,
			'description' => 'Shakala Boom Boom'
		]);
	}

	public function test_vibe_can_be_updated_by_a_member()
	{
		Event::fake();
		$vibe = factory(Vibe::class)->create();
		$this->actingAs($vibe->users->first());
		$this->patch(route('vibe.update', $vibe), [
			'name' => 'Shaka Dance',
            'description' => 'Shakala Boom Boom',
         	'open' => $vibe->open,
            'auto_dj' =>  $vibe->auto_dj
        ])->assertRedirect(Vibe::first()->path());
		$this->assertDatabaseHas('vibes', [
			'id' => $vibe->id,
			'description' => 'Shakala Boom Boom'
		]);
	}

	public function test_vibe_updated_event_is_triggered_when_a_vibe_is_updated()
	{
		Event::fake();
		$vibe = factory(Vibe::class)->create();
		$this->actingAs($vibe->users->first());
		$this->patch(route('vibe.update', $vibe), [
			'name' => 'Shaka Dance',
            'description' => 'Shakala Boom Boom',
         	'open' => $vibe->open,
            'auto_dj' =>  $vibe->auto_dj
        ]);
		Event::assertDispatched(VibeUpdated::class);
	}

	public function test_vibe_can_be_deleted_by_owner()
	{
		$vibe = factory(Vibe::class)->create();
		$owner = $vibe->users()->where('owner', true)->first();
		$this->actingAs($owner);
		$this->delete(route('vibe.destroy', $vibe))
			->assertRedirect(route('index'));
		$this->assertDatabaseMissing('vibes', [
			'id' => $vibe->id,
			'description' => $vibe->description
		]);
	}

	public function test_vibe_cannot_be_deleted_by_user_who_is_not_an_owner()
	{
		$vibe = factory(Vibe::class)->create();
		$this->delete(route('vibe.destroy', $vibe))
			->assertStatus(403);
		$this->assertDatabaseHas('vibes', [
			'id' => $vibe->id,
			'description' => $vibe->description
		]);
	}
}
