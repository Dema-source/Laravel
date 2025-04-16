<?php

use App\Helpers\ResponseHelper;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'isAdmin' => IsAdmin::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Exception $e) {
            $data = [];

            if ($e instanceof ModelNotFoundException) {
                return ResponseHelper::error('Resource not found', $data, 404);
            }
            if ($e instanceof NotFoundHttpException) {
                return ResponseHelper::error('Not found', $data, 404);
            }

            if ($e instanceof InvalidArgumentException) {
                return ResponseHelper::error('Invalid argument provided', $data, 400);
            }


            if ($e instanceof UnauthorizedHttpException) {
                return ResponseHelper::error('Unauthorized access', $data, 401);
            }

            if ($e instanceof AccessDeniedHttpException) {
                return ResponseHelper::error('Access denied', $data, 403);
            }

            // if ($e instanceof HttpResponseException) {
            //     $statusCode = $e->getStatusCode();
            //     switch ($statusCode) {
            //         case 429:
            //             return ResponseHelper::error('Too many requests', $data, 429);
            //         case 503:
            //             return ResponseHelper::error('Service unavailable', $data, 503);
            //         default:
            //             return ResponseHelper::error($message, $data, $statusCode);
            //     }
            // }

            // For any other exceptions, return a generic error response
            return ResponseHelper::error('internal server Error', $data, 500);
        });
    })->create();
