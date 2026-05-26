<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        // Modelo no encontrado
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Recurso no encontrado.',
                'errors'  => null,
            ], 404);
        }

        // Ruta no encontrada
        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Ruta no encontrada.',
                'errors'  => null,
            ], 404);
        }

        // Método HTTP no permitido
        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Método HTTP no permitido.',
                'errors'  => null,
            ], 405);
        }

        // Error de validación
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors'  => $e->errors(),
            ], 422);
        }

        // No autenticado
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado.',
                'errors'  => null,
            ], 401);
        }

        // Cualquier otro error
        return response()->json([
            'success' => false,
            'message' => app()->environment('production')
                ? 'Error interno del servidor.'
                : $e->getMessage(),
            'errors'  => null,
        ], 500);
    }
}