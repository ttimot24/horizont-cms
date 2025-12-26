<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Request;
use App\Model\Settings;

class PluginServiceProvider extends ServiceProvider
{

    protected $kernel;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(\Illuminate\Contracts\Http\Kernel $kernel): void
    {
        $this->kernel = $kernel;

        try {

            if ($this->app->isInstalled()) {
                $this->app->plugins = \App\Model\Plugin::active()->get()->keyBy('root_dir');

                $this->registerPluginAutoloaders();

                $this->registerPluginConfigs();
                $this->registerPluginRoutes();
                $this->registerPluginProviders();
                $this->registerPluginAliases();
                $this->registerPluginMiddlewares();
                $this->registerPluginEvents();
                $this->registerPluginLanguage();
                $this->registerPluginConsoleCommands();
                $this->registerPluginViewPaths();
                $this->registerPluginJSScripts();
            }
        } catch (\Exception $e) {
            if (Settings::get('website_debug') == 1 && !Request::is(config('horizontcms.backend_prefix') . "*")) {
                throw $e;
            } else if (Settings::get('admin_debug') == 1 && Request::is(config('horizontcms.backend_prefix') . "*")) {
                throw $e;
            }
        }
    }

    private function registerPluginConfigs(): void
    {

        foreach ($this->app->plugins as $plugin) {
            foreach (glob($plugin->getPath().'/config/*.php') as $file) {
                $this->mergeConfigFrom(base_path($file), strtolower('plugin:'.$plugin->root_dir.':'.basename($file, '.php')));
            }
        }

    }

    private function registerPluginJSScripts(): void
    {

        $jsPlugins = [];

        foreach (app()->plugins as $plugin) {

            foreach ($plugin->getRegister('injectAdminJs', []) as $js) {
                array_push($jsPlugins, $plugin->getPath() . '/' . $js);
            }
        }

        View::share('jsplugins', $jsPlugins);
    }


    private function registerPluginAutoLoaders(): void
    {


        foreach ($this->app->plugins as $plugin) {

            $autoloader =  base_path($plugin->getPath() . "/vendor/autoload.php");
            if (file_exists($autoloader)) {
                require_once($autoloader);
            }
        }
    }


    private function registerPluginProviders(): void
    {

        foreach ($this->app->plugins as $plugin) {

            foreach ($plugin->getRegister('addProviders', []) as $provider) {
                $this->app->register($provider);
            }
        }
    }

    private function registerPluginMiddlewares(): void
    {

        foreach ($this->app->plugins as $plugin) {

            foreach ($plugin->getRegister('addMiddlewares', []) as $alias => $middleware) {

                $this->kernel->prependMiddleware($middleware);

                $this->kernel->pushMiddleware($middleware);

                $this->app->router->middleware($alias, $middleware);
            }
        }
    }

    public function registerPluginEvents(): void
    {

        foreach ($this->app->plugins as $plugin) {

            foreach ($plugin->getRegister('eventHooks', []) as $key => $value) {
                foreach ($value as $do) {
                    \Event::listen($key, $do);
                }
            }
        }
    }



    private function registerPluginLanguage(): void
    {

        if (Request::is(config('horizontcms.backend_prefix') . "/plugin/run/*")) {

            $plugin = $this->app->plugins->get(studly_case(Request::segment(4)));

            if ($plugin != null) {
                $this->loadTranslationsFrom(base_path($plugin->getPath() . "/resources/lang"), 'plugin');
            }
        }
    }


    private function registerPluginConsoleCommands(): void
    {
        foreach ($this->app->plugins as $plugin) {
            if (!$plugin->isActive()) {
                continue;
            }
            foreach ($plugin->getRegister('cliCommands', []) as $command) {
                $this->commands([$command]);
            }
        }
    }

    public function registerPluginViewPaths(): void
    {


        foreach ($this->app->plugins as $plugin) {
            if (!$plugin->isActive()) {
                continue;
            }

            \View::addNamespace(str_slug($plugin->root_dir), [
                $plugin->getPath() . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "View",
                $plugin->getPath() . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "views",
            ]);
        }
    }

    protected function registerPluginRoutes(): void
    {

        if (!isset($this->app->plugins)) {
            return;
        }

        foreach ($this->app->plugins as $plugin) {

            if (file_exists($plugin->getPath() . '/routes/web.php')) {

                Route::group(
                    $plugin->getRegister('webRouteOptions', ['middleware' => 'web']),
                    function ($router) use ($plugin) {
                        require base_path($plugin->getPath() . '/routes/web.php');
                    }
                );
            }


            if (file_exists($plugin->getPath() . '/routes/api.php')) {

                Route::group(
                    $plugin->getRegister('apiRouteOptions', ['middleware' => 'api']),
                    function ($router) use ($plugin) {
                        require base_path($plugin->getPath() . '/routes/api.php');
                    }
                );
            }
        }
    }

    private function registerPluginAliases(): void
    {

        foreach (app()->plugins as $plugin) {

            foreach ($plugin->getRegister('addAliases', []) as $key => $value) {
                class_alias($value, $key);
            }
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
