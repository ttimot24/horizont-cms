<?php

namespace App\Http;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\BaseUrlMiddleware;
use App\Http\Middleware\EmailConfigMiddleware;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\HttpsMiddleware;
use App\Http\Middleware\InstallerMiddleware;
use App\Http\Middleware\LogLastUserActivity;
use App\Http\Middleware\MenuMiddleware;
use App\Http\Middleware\NavbarPluginMiddleware;
use App\Http\Middleware\PluginMiddleware;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SettingsMiddleware;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Middleware\WebsiteMiddleware;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

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
        HandleCors::class,
        CheckForMaintenanceMode::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            BaseUrlMiddleware::class,
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            TrimStrings::class,
            ConvertEmptyStringsToNull::class,
            InstallerMiddleware::class,
            SettingsMiddleware::class,
            LogLastUserActivity::class,
            HttpsMiddleware::class,
            EmailConfigMiddleware::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
            HandleCors::class,
        ],

        'admin' => [
            Authenticate::class,
            AdminMiddleware::class,
            MenuMiddleware::class,
            NavbarPluginMiddleware::class,
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
        'auth' => Authenticate::class,
        'auth.basic' => AuthenticateWithBasicAuth::class,
        'bindings' => SubstituteBindings::class,
        'can' => Authorize::class,
        'guest' => RedirectIfAuthenticated::class,
        'throttle' => ThrottleRequests::class,
        'website' => WebsiteMiddleware::class,
        'plugin' => PluginMiddleware::class,
    ];
}
