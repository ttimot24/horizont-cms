<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EmailConfigMiddleware
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

        if(\App\HorizontCMS::isInstalled()){
        //    \Config::set('mail.from.address',$request->settings['default_email']);
         //   \Config::set('mail.from.name',$request->settings['site_name']);
        }


        return $next($request);
    }
}
