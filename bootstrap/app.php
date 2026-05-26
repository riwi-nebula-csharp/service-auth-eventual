<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'is.admin'           => \App\Http\Middleware\IsAdmin::class,
            'is.employee'        => \App\Http\Middleware\IsEmployee::class,
            'has.tickets'        => \App\Http\Middleware\HasTicketsPermission::class,
            'has.access'         => \App\Http\Middleware\HasAccessPermission::class,
            'is.internal'        => \App\Http\Middleware\IsInternalService::class,
            'is.authenticated'   => \App\Http\Middleware\IsAuthenticated::class,
        ]);

        $middleware->validateCsrfTokens(except: ['api/*']);

        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {

                if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Recurso no encontrado.',
                        'errors'  => null,
                    ], 404);
                }

                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ruta no encontrada.',
                        'errors'  => null,
                    ], 404);
                }

                if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Método HTTP no permitido.',
                        'errors'  => null,
                    ], 405);
                }

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error de validación.',
                        'errors'  => $e->errors(),
                    ], 422);
                }

                return response()->json([
                    'success' => false,
                    'message' => app()->environment('production')
                        ? 'Error interno del servidor.'
                        : $e->getMessage(),
                    'errors'  => null,
                ], 500);
            }
        });
    })->create();