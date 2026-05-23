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
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();