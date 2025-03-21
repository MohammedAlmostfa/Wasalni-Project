<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $exception)
    {
        // Handle AuthorizationException
        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'errors' => [
                    'errorDetails' => $exception->getMessage(),
                ]
            ], $exception->status ?? 403); // Use status code from exception or default to 403
        }
        // Handle ModelNotFoundException
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'errors' => [
                    'errorDetails' => __("trip.Resource_not_found"),
                ]
            ], 404);
        }

        // Handle other exceptions
        return parent::render($request, $exception);

    }
}
