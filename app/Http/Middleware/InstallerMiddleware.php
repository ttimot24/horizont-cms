<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;

class InstallerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Closure|RedirectResponse
    {

        if(!\App\HorizontCMS::isInstalled() && !$request->is(config('horizontcms.backend_prefix').'/install*')){

            Auth::logout();

            return redirect(route('install.index'));

        }else if(\App\HorizontCMS::isInstalled() && $request->is(config('horizontcms.backend_prefix').'/install*')){

            return redirect(route('login'));
        }


        return $next($request);
    }
}
