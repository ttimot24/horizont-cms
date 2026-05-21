<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\BaseUrlMiddleware::class,
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \App\Http\Middleware\InstallerMiddleware::class,
            \App\Http\Middleware\SettingsMiddleware::class,
            \App\Http\Middleware\LogLastUserActivity::class,
            \App\Http\Middleware\HttpsMiddleware::class,
            \App\Http\Middleware\EmailConfigMiddleware::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
            \Illuminate\Http\Middleware\HandleCors::class,
        ],

        'admin' => [
            \Illuminate\Auth\Middleware\Authenticate::class,
            \App\Http\Middleware\AdminMiddleware::class,
            \App\Http\Middleware\MenuMiddleware::class,
            \App\Http\Middleware\NavbarPluginMiddleware::class,
        ],

    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'website' => \App\Http\Middleware\WebsiteMiddleware::class,
        'plugin' => \App\Http\Middleware\PluginMiddleware::class,
    ];
}
