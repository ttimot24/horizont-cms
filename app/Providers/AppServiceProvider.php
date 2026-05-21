<?php

namespace App\Providers;

use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Config::set('self-update.version_installed', Config::get('horizontcms.version'));

        \Illuminate\Pagination\Paginator::useBootstrap();

        if (! app()->runningInConsole() && ($this->app->environment('local') || $this->app->environment('testing'))) {
            \DB::connection()->enableQueryLog();

            if ($this->app->request->has('sql-debug')) {
                \Event::listen(RequestHandled::class, function (RequestHandled $event): void {
                    $queries = \DB::getQueryLog();
                    dd($queries);
                });
            }
        }

        View::share('title', '');
        View::share('css', Config::get('horizontcms.css'));
        View::share('js', Config::get('horizontcms.js'));

    }

    /**
     * Register any application services.
     */
    public function register(): void {}
}
