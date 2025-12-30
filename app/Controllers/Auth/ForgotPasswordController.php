<?php

namespace App\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use \Illuminate\Contracts\View\View;
use \Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Config;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm(): View|Factory
    {
        return view('auth.passwords.email', [
            'app_name' => Config::get('app.name'),
            'admin_logo' => url(Config::get('horizontcms.admin_logo')),
        ]);
    }
}
