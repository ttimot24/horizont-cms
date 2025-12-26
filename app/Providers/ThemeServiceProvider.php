<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use \App\Services\Theme;
use \App\Model\Settings;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if (app()->isInstalled()) {

            $theme = new Theme(Settings::get('theme'));

            $this->app->singleton(Theme::class, function ($app) use($theme) {
                return $theme;
            });


            $this->registerThemeConfigs($theme);

            $this->registerTranslations($theme);

            $this->registerThemeViews($theme);

            $this->registerThemeRoutes($theme);

        }
    }

    protected function registerThemeConfigs(Theme $theme): void
    {
        foreach (glob($theme->getPath().'/config/*.php') as $file) {
            $this->mergeConfigFrom(base_path($file), strtolower('theme:'.basename($file, '.php')));
        }

    }


    protected function registerTranslations(Theme $theme): void
    {
        if (!Request::is(\Config::get('horizontcms.backend_prefix') . "/*")) {
            $this->loadJsonTranslationsFrom(base_path($theme->getPath() . "resources/lang"));
        }
    }

    protected function registerThemeViews(Theme $theme): void
    {
        \View::addNamespace('theme', [
            $theme->getPath() . "app" . DIRECTORY_SEPARATOR . "View",
            $theme->getPath() . "resources" . DIRECTORY_SEPARATOR . "views",
        ]);
    }

    protected function registerThemeRoutes(Theme $theme): void
    {

        if (file_exists($theme->getPath() . 'routes/web.php')) {

            Route::group(
                ['middleware' => 'web'],
                function ($router) use ($theme) {
                    require base_path($theme->getPath() . '/routes/web.php');
                }
            );
        }

        if (file_exists($theme->getPath() . 'routes/api.php')) {

            Route::group(
                ['middleware' => 'api'],
                function ($router) use ($theme) {
                    require base_path($theme->getPath() . '/routes/api.php');
                }
            );
        }
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
