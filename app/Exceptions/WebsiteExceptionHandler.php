<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class WebsiteExceptionHandler extends Handler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param  Exception  $exception
     * @return Response
     */
    public function render($request, Throwable $exception)
    {

        if ($request->settings['website_debug'] == 1 && view()->exists('theme::errors.exception')) {
            return response()->view('theme::errors.exception', ['exception' => $exception]);
        }

        return parent::render($request, $exception);
    }
}
