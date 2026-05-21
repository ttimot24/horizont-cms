<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class WebsiteExceptionHandler extends Handler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {

        if ($request->settings['website_debug'] == 1 && view()->exists('theme::errors.exception')) {
            return response()->view('theme::errors.exception', ['exception' => $exception]);
        }

        return parent::render($request, $exception);
    }
}
