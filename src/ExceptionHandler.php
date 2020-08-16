<?php

namespace GGbear\Routing;

use Exception;
use GGbear\Routing\Exceptions\BasicAuthenticationException;
use GGbear\Routing\Exceptions\TokenAuthenticationException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionHandler
{
    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $e
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $e)
    {
        // parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {

        $response = new Response();
        $response->header("Content-Type", "application/json");

        if ($e instanceof BasicAuthenticationException) {
            return $response->setContent([
                'error' => true,
                'message' => 'Basic authentication failed',
            ]);
        }

        if ($e instanceof TokenAuthenticationException) {
            return $response->setContent([
                'error' => true,
                'message' => 'Token is invalid',
            ]);
        }

        if ($e instanceof NotFoundHttpException) {
            return $response->setContent([
                'error' => true,
                'message' => 'Invalid route',
            ]);
        }

        return $response->setContent([
            'error' => true,
            'message' => 'Internal Error',
        ]);
    }
}
