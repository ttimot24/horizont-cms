<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if (\App\HorizontCMS::isInstalled()) {

            $this->settings->assignAll();
            $request->settings = json_decode(json_encode($this->settings->settings), true);

            config(['settings' => $this->settings::getAll()]);

            View::share('settings', $this->settings::getAll());
        }

        return $next($request);
    }
}
