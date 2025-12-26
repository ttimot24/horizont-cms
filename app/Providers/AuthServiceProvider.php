<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        \App\Providers\Gates\PermissionsGate::register();

        $prefix = \Config::get('horizontcms.backend_prefix');

        if (
            $this->app->request->is($prefix . '*') && !$this->app->request->is($prefix . '/install*')
            && !$this->app->request->is($prefix . '/login*')
        ) {

            Gate::define('global-authorization', function ($user) use ($prefix) {
                $request = app()->request;

                // Admin dashboard vagy root esetén engedélyezett
                if ($request->segment(2) === null || $request->is($prefix . '/dashboard*')) {
                    return true;
                }

                // Meghatározzuk a modul nevét
                $isPluginRun = $request->is($prefix . '/plugin/run/*');

                $segment = $isPluginRun? $request->segment(4): $request->segment(2);

                $segment = str_replace('-', '', $segment); // kötőjelek eltávolítása

                // Meghatározzuk az akciót (módszert) - pl. view, create stb.
                $action = $request->route()?->getActionMethod(); // pl. index, show, create, store, edit, update, destroy

                // Akció térképezés az engedély típusokhoz
                $actionMap = [
                    'index'   => 'view',
                    'show'    => 'view',
                    'create'  => 'create',
                    'store'   => 'create',
                    'edit'    => 'update',
                    'update'  => 'update',
                    'destroy' => 'delete',
                    'delete'  => 'delete',
                ];

                $mappedAction = $actionMap[$action] ?? null;

                if (!$mappedAction) {
                    return false; // Ismeretlen akció
                }

                // Teljes permission kulcs: pl. blogpost.view
                $permissionKey = "{$segment}.{$mappedAction}";

                return in_array($permissionKey, $user->role->rights);
            });
        }
    }
}
