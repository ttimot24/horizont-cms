<?php

namespace App\Providers;

use App\Interfaces\ThemeEngineInterface;
use App\Services\Theme;
use Illuminate\Support\ServiceProvider;

class ThemeEngineServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        $this->app->singleton(ThemeEngineInterface::class, function ($app) {

            $theme = app()->make(Theme::class);

            $engine = new (config()->get('horizontcms.theme_engines.'.config('horizontcms.default_theme_engine')))($this->app->request);

            $engine->setTheme($theme);

            return $engine;
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void {}
}
