<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ThemeEngineServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {

        $this->app->singleton(\App\Interfaces\ThemeEngineInterface::class, function ($app) {

            $theme = app()->make(\App\Services\Theme::class);

            $engine = new (config()->get('horizontcms.theme_engines.' . config('horizontcms.default_theme_engine')))($this->app->request);

            $engine->setTheme($theme);

            return $engine;
        });
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
