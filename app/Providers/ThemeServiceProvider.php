<?php

namespace App\Providers;

use App\Model\Settings;
use App\Services\Theme;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->isInstalled()) {

            $theme = new Theme(Settings::get('theme'));

            $this->app->singleton(Theme::class, function ($app) use ($theme) {
                return $theme;
            });

            $this->registerThemeAutoLoader($theme);

            $this->registerThemeConfigs($theme);

            $this->registerTranslations($theme);

            $this->registerThemeViews($theme);

            $this->registerThemeRoutes($theme);

            $this->registerThemeCommands($theme);

            $this->registerThemeProviders($theme);

        }
    }

    private function registerThemeAutoLoader(Theme $theme): void
    {
        $autoloader = base_path($theme->getPath().'/vendor/autoload.php');
        if (file_exists($autoloader)) {
            require_once $autoloader;
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
        if (! Request::is(Config::get('horizontcms.backend_prefix').'/*')) {
            $this->loadJsonTranslationsFrom(base_path($theme->getPath().'resources/lang'));
        }
    }

    protected function registerThemeViews(Theme $theme): void
    {
        View::addNamespace('theme', [
            $theme->getPath().'app'.DIRECTORY_SEPARATOR.'View',
            $theme->getPath().'resources'.DIRECTORY_SEPARATOR.'views',
        ]);
    }

    protected function registerThemeRoutes(Theme $theme): void
    {

        if (file_exists($theme->getPath().'routes/web.php')) {

            Route::group(
                ['middleware' => 'web'],
                function ($router) use ($theme): void {
                    require base_path($theme->getPath().'/routes/web.php');
                }
            );
        }

        if (file_exists($theme->getPath().'routes/api.php')) {

            Route::group(
                ['middleware' => 'api'],
                function ($router) use ($theme): void {
                    require base_path($theme->getPath().'/routes/api.php');
                }
            );
        }
    }

    private function registerThemeProviders(Theme $theme): void
    {
        foreach ($theme->getProviders() as $provider) {
            $this->app->register($provider);
        }

    }

    protected function registerThemeCommands(Theme $theme): void
    {

        $commands = [];

        foreach (glob($theme->getPath().'/Console/*.php') as $file) {

            $class = $this->resolveClassFromThemeFile($theme, $file);

            if (class_exists($class)) {
                $commands[] = $class;
            }
        }

        $this->commands($commands);
    }

    protected function resolveClassFromThemeFile(Theme $theme, string $file): string
    {
        $relative = str_replace($theme->getPath().'/', '', $file);

        return 'Theme\\'.$theme->getName().'\\'.
            str_replace(['/', '.php'], ['\\', ''], $relative);
    }

    /**
     * Register any application services.
     */
    public function register(): void {}
}
