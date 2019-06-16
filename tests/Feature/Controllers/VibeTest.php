<?php

namespace Tests\Feature\Controllers;

use App\Vibe;
use App\User;
use App\MusicAPI\Playlist;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Access\AuthorizationException;

class VibeTest extends TestCase
{
	use WithFaker, RefreshDatabase;

	public function test_vibe_can_be_created()
	{
		Event::fake();
		$playlist = app(Playlist::class)->create('Party');
		$attributes = factory(Vibe::class)->raw([
			'name' => $playlist->name, 
			'api_id' => $playlist->id
		]);
		$this->post(route('vibe.store'), $attributes)
			->assertRedirect(Vibe::first()->path);
		$this->assertDatabaseHas('vibes', [
			'api_id' => $attributes['api_id'],
			'description' => $attributes['description']
		]);
	}

	public function test_vibe_can_be_viewed_by_a_user()
	{
		$vibe = factory(Vibe::class)->create();
		$this->get($vibe->path)
			->assertSuccessful()
			->assertSee($vibe->description);
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
		$user = factory(User::class)->create();
		$vibe->users()->attach($user->id, ['owner' => true]);

		$this->actingAs($vibe->users->first());
		$this->patch(route('vibe.update', $vibe), [
			'name' => 'Shaka Dance',
            'description' => 'Shakala Boom Boom',
         	'open' => $vibe->open,
            'auto_dj' =>  $vibe->auto_dj
        ])->assertRedirect(Vibe::first()->path);
		$this->assertDatabaseHas('vibes', [
			'id' => $vibe->id,
			'description' => 'Shakala Boom Boom'
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

	public function test_vibe_can_be_deleted_by_owner()
	{
		$vibe = factory(Vibe::class)->create();
		$user = factory(User::class)->create();
		$vibe->users()->attach($user->id, ['owner' => true]);
		$owner = $vibe->users()->where('owner', true)->first();
		
		$this->actingAs($owner);
		$this->delete(route('vibe.destroy', $vibe))
			->assertRedirect(route('index'));
		$this->assertDatabaseMissing('vibes', [
			'id' => $vibe->id,
			'description' => $vibe->description
		]);
	}
}
