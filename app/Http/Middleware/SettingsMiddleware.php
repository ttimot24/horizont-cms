<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\View;

class SettingsMiddleware
{

    private $settings;

    public function __construct(\App\Model\Settings $settings)
    {
        $this->settings = $settings;
    }


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

            $this->settings->assignAll();
            $request->settings = json_decode(json_encode($this->settings->settings), true);
           
            View::share('settings', $this->settings::getAll());
        }


        return $next($request);
    }
}
