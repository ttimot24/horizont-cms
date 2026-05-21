<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class UserEventListener
{
    private Request $request;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function countLogin(Login $event): void
    {
        if (! $this->request->expectsJson()) {
            $event->user->increment('visits');
        }
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        //
    }
}
