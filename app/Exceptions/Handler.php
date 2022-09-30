<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */

    protected function shouldReturnJson($request, Throwable $e)
    {
        return true;
    }
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            $this->renderable(function (AuthenticationException $e, $request) {
                if ($request->is('api/*')) {
                    return response()->json([
                      'status_code' => 401,
                      'success' => false,
                      'message' => 'Unauthenticated.'
                    ], 401);
                }
               });
        });
    }
}
