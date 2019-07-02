<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use App\User;

class AuthoriseTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        Auth::logout();
    }

    public function test_user_trying_to_be_authorised_with_spotify_is_redirected_to_the_spotify_authorisation_url()
    {
        $this->get(route('authorise'))
            ->assertRedirect('https://accounts.spotify.com/authorize?client_id=123');
    }

    public function test_unauthenticated_user_who_tries_to_access_the_welcome_page_can_access_the_welcome_page()
    {
        $this->get(route('welcome'))
            ->assertViewIs('welcome');
    }

    public function test_authenticated_user_who_tries_to_access_the_welcome_page_is_redirected_to_home()
    {
        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);
        $this->get(route('welcome'))
            ->assertRedirect(route('home'));
    }
}
