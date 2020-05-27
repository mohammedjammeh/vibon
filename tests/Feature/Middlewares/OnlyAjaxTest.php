<?php

namespace Tests\Feature\Middlewares;

use App\Vibe;
use Illuminate\Http\Request;
use App\Http\Middleware\OnlyAjax;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OnlyAjaxTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_the_only_ajax_middleware_ensures_that_none_xml_http_requests_cannot_hit_the_controllers()
    {
        $this->expectException(HttpException::class);
        $request = Request::create(route('index'), 'GET');
        app(OnlyAjax::class)->handle($request, function () {});
    }

    public function test_the_only_ajax_middleware_allows_xml_http_requests_to_hit_the_controllers()
    {
        $request = Request::create(route('index'), 'GET', [], [], [], $this->defaultHeaders);
        app(OnlyAjax::class)->handle($request, function () {});
        $this->addToAssertionCount(1);
    }
}
