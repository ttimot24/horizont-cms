<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class BaseUrlMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Closure
    {

        $base_url = "//".$request->headers->get('host').$request->getBaseUrl()."/";
        Config::set('app.url',$base_url);

        return $next($request);
    }
}
