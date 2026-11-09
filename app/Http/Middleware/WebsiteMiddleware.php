<?php

namespace App\Http\Middleware;

use App\Services\ShortCode;
use Closure;
use Illuminate\Http\Request;

class WebsiteMiddleware
{
    private $widgets;

    public function __construct(ShortCode $shortcode_engine)
    {
        $this->widgets = $shortcode_engine;
    }

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $this->widgets->initalize(app()->plugins);

        $response->setContent($this->widgets->compile($response->getContent()));

        return $response;
    }
}
