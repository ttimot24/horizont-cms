<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleLoaderServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        require_once(base_path('bootstrap/loader.php'));
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
       spl_autoload_register('module_loader');


       require_once app_path('Helpers/Functions/link.php');
    }

}
