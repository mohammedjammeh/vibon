<?php

namespace Tests\Feature\Middlewares;

use Illuminate\Auth\AuthenticationException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Middleware\Authenticate;

class AuthenticationCheckTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->app->extend('SpotifySession', function () {
            return new Session();
        });
    }

    public function test_unauthenticated_user_is_redirected_to_welcome_page_by_authenticate_middleware()
    {
        $this->expectException(AuthenticationException::class);
        Auth::logout();
        $request = Request::create(route('index'), 'GET');
        $response = app(Authenticate::class)->handle($request, function () {});
        $this->assertEquals($response->getStatusCode(), 302);
    }
}
