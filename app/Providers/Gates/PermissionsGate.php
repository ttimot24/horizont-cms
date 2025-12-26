<?php

namespace App\Providers\Gates;

use Illuminate\Support\Facades\Gate;

class PermissionsGate
{
    public static function register(): void
    {
        Gate::define('access', function ($user, string $permission) {
            return $user->hasPermission($permission);
        });

        Gate::define('view', fn($user, $resource) => $user->hasPermission("{$resource}.view"));
        Gate::define('create', fn($user, $resource) => $user->hasPermission("{$resource}.create"));
        Gate::define('update', fn($user, $resource) => $user->hasPermission("{$resource}.update"));
        Gate::define('delete', fn($user, $resource) => $user->hasPermission("{$resource}.delete"));

        Gate::define('admin-area',function($user) {
            return $user->isAdmin() && $user->isActive() && $user->hasPermission('admin_area');
        });

    }
}