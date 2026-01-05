<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Closure|RedirectResponse{ 

        if($request->user()->isAdmin() && $request->user()->isActive()){
            return $next($request);
        }

        Auth::logout();
        return redirect()->back();
    }
}
