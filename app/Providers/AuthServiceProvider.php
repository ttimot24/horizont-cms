<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;

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

        $prefix = Config::get('horizontcms.backend_prefix');

        if (
            $this->app->request->is($prefix . '*') && !$this->app->request->is($prefix . '/install*')
            && !$this->app->request->is($prefix . '/login*')
        ) {

            Gate::define('global-authorization', function ($user) use ($prefix) {
                $request = app()->request;

                if ($request->segment(2) === null || $request->is($prefix . '/dashboard*')) {
                    return true;
                }

                $isPluginRun = $request->is($prefix . '/plugin/run/*');

                $segment = $isPluginRun? $request->segment(4): $request->segment(2);

                $segment = str_replace('-', '', $segment);

                $action = $request->route()?->getActionMethod(); // index, show, create, store, edit, update, destroy

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
                    return false;
                }

                $permissionKey = "{$segment}.{$mappedAction}";

                return in_array($permissionKey, $user->role->rights);
            });
        }
    }
}
