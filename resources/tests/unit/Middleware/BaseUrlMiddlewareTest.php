<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\BaseUrlMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class BaseUrlMiddlewareTest extends \TestCase
{
    public function testBaseUrlIsSetCorrectly()
    {

        Config::set('app.url', null);

        $request = Request::create('/admin/panel', 'GET');
        $request->headers->set('host', 'example.com');

        $middleware = new BaseUrlMiddleware;

        $response = $middleware->handle($request, function ($req) {
            return $req;
        });

        $expected = "//example.com/";

        $this->assertEquals($expected, Config::get('app.url'));

        $this->assertSame($request, $response);
    }
}
