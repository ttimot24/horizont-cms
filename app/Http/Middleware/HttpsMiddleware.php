<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class HttpsMiddleware
{

    public function handle(Request $request, Closure $next): Closure|RedirectResponse {

        if(\App\HorizontCMS::isInstalled()){

	        if ($request->settings['use_https']==1 && !$request->secure()) {
	            return redirect()->secure($request->getRequestUri());
	        }

	        if($request->settings['use_https']==1){
	        	\URL::forceScheme('https');
	        }

   		}

        return $next($request); 
    }

}