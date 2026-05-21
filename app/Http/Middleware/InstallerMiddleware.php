<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstallerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if (! \App\HorizontCMS::isInstalled() && ! $request->is(config('horizontcms.backend_prefix').'/install*')) {

            Auth::logout();

            return redirect(route('install.index'));

        } elseif (\App\HorizontCMS::isInstalled() && $request->is(config('horizontcms.backend_prefix').'/install*')) {

            return redirect(route('login'));
        }

        return $next($request);
    }
}
