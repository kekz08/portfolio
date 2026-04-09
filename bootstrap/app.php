<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $exception, $request) {
            if (!($request->expectsJson() || $request->is('api/*'))) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $exception->errors(),
            ], 422);
        });

        $exceptions->render(function (AuthenticationException $exception, $request) {
            if (!($request->expectsJson() || $request->is('api/*'))) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        });

        $exceptions->render(function (AuthorizationException $exception, $request) {
            if (!($request->expectsJson() || $request->is('api/*'))) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Forbidden.',
            ], 403);
        });

        $exceptions->render(function (NotFoundHttpException $exception, $request) {
            if (!($request->expectsJson() || $request->is('api/*'))) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Endpoint not found.',
            ], 404);
        });

        $exceptions->render(function (HttpExceptionInterface $exception, $request) {
            if (!($request->expectsJson() || $request->is('api/*'))) {
                return null;
            }

            if (in_array($exception->getStatusCode(), [401, 403, 404, 422], true)) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage() !== '' ? $exception->getMessage() : 'Request failed.',
            ], $exception->getStatusCode());
        });
    })->create();
