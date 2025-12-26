<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {

        \Config::set('self-update.version_installed', \Config::get('horizontcms.version'));

        \Illuminate\Pagination\Paginator::useBootstrap();

        if (!app()->runningInConsole() && ($this->app->environment("local") || $this->app->environment("testing")) ) {
                \DB::connection()->enableQueryLog();

                if ( $this->app->request->has('sql-debug') ) {
                    \Event::listen(RequestHandled::class, function(RequestHandled $event) {
                            $queries = \DB::getQueryLog();
                            dd($queries);
                    });
                }
        }


        View::share('title', "");
        View::share('css', \Config::get('horizontcms.css'));
        View::share('js', \Config::get('horizontcms.js'));
        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {

    }
}
